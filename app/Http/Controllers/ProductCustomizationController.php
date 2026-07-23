<?php

namespace App\Http\Controllers;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;
use App\Models\ProductCustomization;
use App\Services\CartService;
use App\Services\ProductCustomizationService;
use App\Support\PrintfulVariantOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class ProductCustomizationController extends Controller
{
    public function __construct(
        private readonly ProductCustomizationService $customizations,
        private readonly CartService $cart,
    ) {}

    public function show(PrintfulProduct $printfulProduct): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', __('Please log in to customize this product.'));
        }

        $printfulProduct->load('variants');
        $options = PrintfulVariantOptions::forProduct($printfulProduct);

        return view('products.customize', [
            'product' => $printfulProduct,
            'options' => $options,
            'customizationFee' => $this->customizations->feeAmount(),
        ]);
    }

    public function options(PrintfulProduct $printfulProduct): JsonResponse
    {
        $printfulProduct->load('variants');

        return response()->json([
            'success' => true,
            'data' => PrintfulVariantOptions::forProduct($printfulProduct),
        ]);
    }

    public function store(Request $request, PrintfulProduct $printfulProduct): JsonResponse
    {
        $validated = $request->validate([
            'uuid' => ['nullable', 'uuid'],
            'printful_variant_id' => ['nullable', 'integer', 'exists:printful_variants,id'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'placement' => ['nullable', 'string', 'max:100'],
            'canvas_json' => ['nullable', 'string'],
            'text_settings' => ['nullable', 'array'],
            'image_settings' => ['nullable', 'array'],
            'print_area' => ['nullable', 'array'],
            'preview_data_url' => ['nullable', 'string'],
            'print_data_url' => ['nullable', 'string'],
            'design' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:10240'],
        ]);

        if ($request->hasFile('design')) {
            $file = $request->file('design');
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => __('The uploaded file is not a valid image.'),
                ], 422);
            }
            if (($imageInfo[0] ?? 0) < 100 || ($imageInfo[1] ?? 0) < 100) {
                return response()->json([
                    'success' => false,
                    'message' => __('Image dimensions must be at least 100×100 pixels.'),
                ], 422);
            }
        }

        try {
            $customization = $this->customizations->saveDraft(
                $request->user(),
                $printfulProduct,
                $validated,
                $request->file('design'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => __('Could not save customization. Please try again.'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => __('Customization saved.'),
            'data' => $this->payload($customization),
        ]);
    }

    public function finalize(Request $request, ProductCustomization $customization): JsonResponse
    {
        Gate::authorize('update', $customization);

        $validated = $request->validate([
            'preview_data_url' => ['nullable', 'string'],
            'print_data_url' => ['nullable', 'string'],
            'canvas_json' => ['nullable', 'string'],
        ]);

        try {
            if ($validated !== []) {
                $customization = $this->customizations->saveDraft(
                    $request->user(),
                    $customization->product,
                    array_merge($validated, [
                        'uuid' => $customization->uuid,
                        'printful_variant_id' => $customization->printful_variant_id,
                        'color' => $customization->color,
                        'size' => $customization->size,
                        'placement' => $customization->placement,
                    ]),
                );
            }

            $customization = $this->customizations->finalize($customization);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => __('Could not finalize customization.'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => __('Customization finalized.'),
            'data' => $this->payload($customization),
        ]);
    }

    public function addToCart(Request $request, ProductCustomization $customization): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $customization);

        if (! $customization->isFinalized()) {
            try {
                $customization = $this->customizations->finalize($customization);
            } catch (InvalidArgumentException $exception) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
                }

                return back()->with('error', $exception->getMessage());
            }
        }

        $variant = PrintfulVariant::query()->findOrFail($customization->printful_variant_id);
        $quantity = max(1, (int) $request->input('quantity', 1));

        $this->cart->addCustomized($variant, $customization, $quantity);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Customized product added to cart.'),
                'redirect' => route('cart.index'),
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', __('Customized product added to cart.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(ProductCustomization $customization): array
    {
        return [
            'uuid' => $customization->uuid,
            'id' => $customization->id,
            'status' => $customization->status,
            'color' => $customization->color,
            'size' => $customization->size,
            'placement' => $customization->placement,
            'printful_variant_id' => $customization->printful_variant_id,
            'preview_url' => $customization->previewUrl(),
            'print_file_url' => $customization->printFileUrl(),
            'upload_url' => $customization->uploadUrl(),
            'customization_fee' => (float) $customization->customization_fee,
        ];
    }
}
