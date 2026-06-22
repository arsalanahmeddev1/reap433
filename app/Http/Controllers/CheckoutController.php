<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\OrderEmailService;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderEmailService $orderEmails,
        private readonly StripePaymentService $stripe,
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Your cart is empty.'));
        }

        return view('checkout.index', [
            'items' => $this->cart->all(),
            'subtotal' => $this->cart->subtotal(),
            'stripeEnabled' => $this->stripe->isConfigured(),
        ]);
    }

    public function paymentIntent(Request $request): JsonResponse
    {
        if ($this->cart->count() === 0) {
            return response()->json(['message' => __('Your cart is empty.')], 422);
        }

        if (! $this->stripe->isConfigured()) {
            return response()->json(['message' => __('Card payment is not configured.')], 422);
        }

        $cartItems = $this->cart->all();
        $currency = $this->resolveCartCurrency($cartItems);
        $amountCents = $this->amountToCents($this->cart->subtotalFor($cartItems));

        $result = $this->stripe->createPaymentIntent($amountCents, $currency, [
            'user_id' => (string) $request->user()->id,
        ]);

        if (! $result['success']) {
            return response()->json(['message' => $result['error']], 422);
        }

        return response()->json([
            'clientSecret' => $result['client_secret'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Your cart is empty.'));
        }

        if ($request->filled('country_code')) {
            $request->merge([
                'country_code' => strtoupper($request->string('country_code')->toString()),
            ]);
        }

        $request->merge(['country_code' => 'US']);

        if ($request->filled('state_code')) {
            $request->merge([
                'state_code' => strtoupper($request->string('state_code')->toString()),
            ]);
        }

        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address1' => ['required', 'string', 'max:500'],
            'address2' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state_code' => ['required', 'string', 'size:2', 'alpha'],
            'country_code' => ['required', 'string', 'in:US'],
            'zip' => ['required', 'string', 'max:20'],
        ];

        if ($this->stripe->isConfigured()) {
            $rules['payment_intent_id'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $validated['email'] = $request->user()->email;

        $cartItems = $this->cart->all();

        if ($cartItems === []) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Your cart is empty.'));
        }

        $subtotal = $this->cart->subtotalFor($cartItems);
        $currency = $this->resolveCartCurrency($cartItems);
        $paymentIntentId = $validated['payment_intent_id'] ?? null;
        $isPaid = false;

        if ($this->stripe->isConfigured()) {
            $verification = $this->stripe->verifyPaymentIntent(
                $paymentIntentId,
                $this->amountToCents($subtotal),
                $currency,
            );

            if (! $verification['success']) {
                return redirect()
                    ->route('checkout.index')
                    ->withInput($validated)
                    ->with('error', $verification['error'] ?? __('Payment verification failed.'));
            }

            $isPaid = true;
        }

        try {
            $order = DB::transaction(function () use ($validated, $cartItems, $subtotal, $currency, $isPaid, $paymentIntentId) {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_name' => $validated['full_name'],
                    'customer_email' => $validated['email'],
                    'customer_phone' => $validated['phone'] ?? null,
                    'address1' => $validated['address1'],
                    'address2' => $validated['address2'] ?? null,
                    'city' => $validated['city'],
                    'state_code' => $validated['state_code'],
                    'country_code' => $validated['country_code'],
                    'zip' => $validated['zip'],
                    'subtotal' => $subtotal,
                    'currency' => $currency,
                    'status' => $isPaid ? 'processing' : 'pending_payment',
                    'payment_status' => $isPaid ? 'paid' : 'unpaid',
                    'payment_method' => $isPaid ? 'stripe' : null,
                    'stripe_payment_intent_id' => $isPaid ? $paymentIntentId : null,
                ]);

                foreach ($cartItems as $item) {
                    $price = (float) $item['price'];
                    $quantity = (int) $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'printful_product_id' => $item['product_id'] ?? null,
                        'printful_variant_id' => $item['printful_variant_id'] ?? $item['variant_id'] ?? null,
                        'product_name' => $item['product_name'],
                        'variant_name' => $item['variant_name'] ?? null,
                        'sku' => $item['sku'] ?? null,
                        'price' => $price,
                        'quantity' => $quantity,
                        'total' => round($price * $quantity, 2),
                        'raw_data' => $item,
                    ]);
                }

                return $order;
            });

            $this->cart->clear();

            $this->orderEmails->sendOrderPlaced($order);

            return redirect()
                ->route('order.thank-you', $order)
                ->with('placed_order_number', $order->order_number);
        } catch (Throwable $exception) {
            Log::error('Checkout order creation failed', [
                'email' => $validated['email'] ?? null,
                'cart_count' => count($cartItems),
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            return redirect()
                ->route('checkout.index')
                ->withInput($validated)
                ->with('error', __('We could not place your order. Please try again.'));
        }
    }

    public function thankYou(Request $request, Order $order): View
    {
        $ownsOrder = strcasecmp((string) $order->customer_email, (string) $request->user()->email) === 0;
        $justPlaced = session('placed_order_number') === $order->order_number;

        if (! $ownsOrder && ! $justPlaced) {
            abort(403);
        }

        session()->forget('placed_order_number');

        $order->load('items');

        return view('checkout.thank-you', [
            'order' => $order,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cartItems
     */
    private function resolveCartCurrency(array $cartItems): string
    {
        foreach ($cartItems as $item) {
            if (! empty($item['currency'])) {
                return strtoupper(substr((string) $item['currency'], 0, 3));
            }
        }

        return 'USD';
    }

    private function amountToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
