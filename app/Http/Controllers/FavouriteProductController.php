<?php

namespace App\Http\Controllers;

use App\Models\FavouriteProduct;
use App\Models\PrintfulProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavouriteProductController extends Controller
{
    public function index(Request $request): View
    {
        $favourites = FavouriteProduct::query()
            ->where('user_id', $request->user()->id)
            ->with(['product.variants'])
            ->latest()
            ->paginate(10);

        return view('profile.favourites.index', [
            'user' => $request->user(),
            'favourites' => $favourites,
        ]);
    }

    public function store(Request $request, PrintfulProduct $printfulProduct): JsonResponse|RedirectResponse
    {
        $result = $this->addFavourite($request->user()->id, $printfulProduct);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_favourite' => true,
                'message' => $result['message'],
            ]);
        }

        return back()->with('success', $result['message']);
    }

    public function destroy(Request $request, PrintfulProduct $printfulProduct): JsonResponse|RedirectResponse
    {
        FavouriteProduct::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $printfulProduct->id)
            ->firstOrFail()
            ->delete();

        $message = __('Product removed from favourites.');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_favourite' => false,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function toggle(Request $request, PrintfulProduct $printfulProduct): JsonResponse|RedirectResponse
    {
        $favourite = FavouriteProduct::withTrashed()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $printfulProduct->id)
            ->first();

        if ($favourite && ! $favourite->trashed()) {
            $favourite->delete();
            $isFavourite = false;
            $message = __('Product removed from favourites.');
        } else {
            $result = $this->addFavourite($request->user()->id, $printfulProduct, $favourite);
            $isFavourite = true;
            $message = $result['message'];
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_favourite' => $isFavourite,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Store intended URL and send guests to the existing login page.
     */
    public function continueLogin(Request $request): RedirectResponse
    {
        $redirect = $request->query('redirect');

        if (is_string($redirect) && $redirect !== '') {
            $appUrl = rtrim((string) config('app.url'), '/');
            $isAbsolute = str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://');

            if ($isAbsolute) {
                if (str_starts_with($redirect, $appUrl.'/') || $redirect === $appUrl) {
                    $request->session()->put('url.intended', $redirect);
                }
            } elseif (str_starts_with($redirect, '/')) {
                $request->session()->put('url.intended', url($redirect));
            }
        }

        return redirect()->route('login');
    }

    /**
     * @return array{message: string, created: bool}
     */
    private function addFavourite(int $userId, PrintfulProduct $product, ?FavouriteProduct $favourite = null): array
    {
        $favourite ??= FavouriteProduct::withTrashed()
            ->where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($favourite) {
            if ($favourite->trashed()) {
                $favourite->restore();

                return [
                    'message' => __('Product added to favourites.'),
                    'created' => false,
                ];
            }

            return [
                'message' => __('Product is already in your favourites.'),
                'created' => false,
            ];
        }

        FavouriteProduct::create([
            'user_id' => $userId,
            'product_id' => $product->id,
        ]);

        return [
            'message' => __('Product added to favourites.'),
            'created' => true,
        ];
    }
}
