<?php

namespace App\Http\Controllers;

use App\Mail\GuestCheckoutWelcomeMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function index()
    {
        $checkout = Cart::current();
        $user = Auth::user();

        if (! $checkout || $checkout->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', __('Your cart is empty.'));
        }

        $checkout->load(['items.product.images']);

        $addresses = $user
            ? $user->addresses()->orderByDesc('is_default')->latest()->get()
            : collect();

        return view('screens.web.checkout.index', compact('checkout', 'user', 'addresses'));
    }

    public function success(Order $order)
    {
        if (! Auth::check()) {
            abort(403);
        }

        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('screens.web.order-success.index', compact('order'));
    }

    public function placeOrder(Request $request): JsonResponse
    {
        $validated = $this->validateCheckoutAddress($request);

        $cart = Cart::current();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['message' => __('Your cart is empty.')], 422);
        }

        try {
            $order = $this->createOrderFromCart(
                Auth::user(),
                $cart,
                $validated,
                paymentMethod: 'manual',
                paymentStatus: 'pending',
                paymentIntentId: 'manual-'.Str::uuid(),
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Order failed'),
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }

    public function createPaymentIntent()
    {
        if (! config('services.stripe.secret')) {
            return response()->json(['message' => __('Stripe is not configured.')], 503);
        }

        $cart = Cart::current();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart empty'], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => (int) ($cart->total() * 100),
            'currency' => 'usd',
            'metadata' => [
                'user_id' => Auth::check() ? (string) Auth::id() : 'guest',
                'cart_id' => (string) $cart->id,
            ],
        ]);

        return response()->json([
            'clientSecret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ]);
    }

    public function storeAfterPayment(Request $request): JsonResponse
    {
        if (! config('services.stripe.secret')) {
            return response()->json(['message' => __('Stripe is not configured.')], 503);
        }

        $validated = $this->validateCheckoutAddress($request);
        $validated['payment_intent_id'] = $request->validate([
            'payment_intent_id' => ['required', 'string'],
        ])['payment_intent_id'];

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($validated['payment_intent_id']);

        if ($intent->status !== 'succeeded') {
            return response()->json([
                'message' => __('Payment not completed'),
            ], 422);
        }

        $existingOrder = Order::query()
            ->where('payment_intent_id', $intent->id)
            ->first();

        if ($existingOrder) {
            return response()->json([
                'success' => true,
                'order_id' => $existingOrder->id,
            ]);
        }

        $cart = Cart::current();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => __('Cart empty — if you were charged, contact support with your payment receipt.'),
            ], 422);
        }

        $newAccountPassword = null;
        $user = Auth::user();

        if (! $user) {
            $email = mb_strtolower(trim($validated['billing_email']));
            $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

            if (! $user) {
                $newAccountPassword = Str::password(14, true, true, true, false);
                $user = User::create([
                    'name' => $validated['billing_name'],
                    'email' => $email,
                    'password' => $newAccountPassword,
                    'role' => config('roles.user'),
                ]);
            }
        }

        try {
            $order = $this->createOrderFromCart(
                $user,
                $cart,
                $validated,
                paymentMethod: 'stripe',
                paymentStatus: 'paid',
                paymentIntentId: $intent->id,
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Order failed'),
                'error' => $e->getMessage(),
            ], 500);
        }

        if ($newAccountPassword !== null) {
            try {
                Mail::to($user->email)->send(new GuestCheckoutWelcomeMail($user, $newAccountPassword, $order));
            } catch (\Throwable $e) {
                Log::error('Guest checkout welcome email failed', [
                    'message' => $e->getMessage(),
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);
            }
        }

        return $this->checkoutSuccessResponse($request, $order, $newAccountPassword);
    }

    private function validateCheckoutAddress(Request $request): array
    {
        $validated = $request->validate([
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'max:50'],
            'billing_address' => ['required', 'string', 'max:500'],
            'billing_city' => ['required', 'string', 'max:100'],
            'billing_state' => ['nullable', 'string', 'max:100'],
            'billing_zip' => ['required', 'string', 'max:20'],
            'billing_country' => ['required', 'string', 'max:100'],
            'shipping_name' => ['nullable', 'string', 'max:255'],
            'shipping_email' => ['nullable', 'email', 'max:255'],
            'shipping_phone' => ['nullable', 'string', 'max:50'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_zip' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['shipping_name'] = $validated['shipping_name'] ?? $validated['billing_name'];
        $validated['shipping_email'] = $validated['shipping_email'] ?? $validated['billing_email'];
        $validated['shipping_phone'] = $validated['shipping_phone'] ?? $validated['billing_phone'];
        $validated['shipping_address'] = $validated['shipping_address'] ?? $validated['billing_address'];
        $validated['shipping_city'] = $validated['shipping_city'] ?? $validated['billing_city'];
        $validated['shipping_state'] = $validated['shipping_state'] ?? $validated['billing_state'];
        $validated['shipping_zip'] = $validated['shipping_zip'] ?? $validated['billing_zip'];
        $validated['shipping_country'] = $validated['shipping_country'] ?? $validated['billing_country'];

        return $validated;
    }

    private function createOrderFromCart(
        User $user,
        Cart $cart,
        array $validated,
        string $paymentMethod,
        string $paymentStatus,
        string $paymentIntentId,
    ): Order {
        $cart->loadMissing('items');

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'total_qty' => $cart->items->sum('qty'),
                'tax' => 0,
                'discount' => 0,
                'total' => $cart->total(),
                'payment_status' => $paymentStatus,
                'order_status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_intent_id' => $paymentIntentId,
            ]);

            $order->addresses()->create([
                'billing_name' => $validated['billing_name'],
                'billing_email' => $validated['billing_email'],
                'billing_phone' => $validated['billing_phone'],
                'billing_address' => $validated['billing_address'],
                'billing_city' => $validated['billing_city'],
                'billing_state' => $validated['billing_state'] ?? '',
                'billing_zip' => $validated['billing_zip'],
                'billing_country' => $validated['billing_country'],
                'shipping_name' => $validated['shipping_name'],
                'shipping_email' => $validated['shipping_email'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? '',
                'shipping_zip' => $validated['shipping_zip'],
                'shipping_country' => $validated['shipping_country'],
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'total' => $item->subtotal,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        return $order;
    }

    private function checkoutSuccessResponse(Request $request, Order $order, ?string $newAccountPassword): JsonResponse
    {
        $order->loadMissing('user');

        if (Auth::check()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        }

        if ($newAccountPassword !== null) {
            Auth::login($order->user);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
            ]);
        }

        $request->session()->put('post_checkout_order_id', $order->id);

        return response()->json([
            'success' => true,
            'login_required' => true,
            'redirect_url' => route('login'),
            'message' => __('Your order is placed. Sign in with your existing password to view your confirmation.'),
        ]);
    }
}
