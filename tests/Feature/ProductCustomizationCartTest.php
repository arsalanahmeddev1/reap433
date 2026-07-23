<?php

namespace Tests\Feature;

use App\Models\PrintfulProduct;
use App\Models\PrintfulVariant;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCustomizationCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_and_customized_cart_lines_do_not_merge_incorrectly(): void
    {
        $user = User::factory()->create();
        $product = PrintfulProduct::query()->create([
            'printful_product_id' => 1001,
            'name' => 'Test Cap',
            'is_synced' => true,
        ]);
        $variant = PrintfulVariant::query()->create([
            'printful_product_id' => $product->id,
            'printful_variant_id' => 2001,
            'name' => 'Test Cap / Black',
            'retail_price' => 20,
            'currency' => 'USD',
            'raw_data' => [
                'color' => 'Black',
                'size' => 'One size',
                'variant_id' => 3001,
                'product' => ['product_id' => 91, 'variant_id' => 3001],
                'files' => [['type' => 'front_dtf_hat']],
            ],
        ]);

        $cart = app(CartService::class);
        $cart->add($variant, 1);
        $this->assertCount(1, $cart->all());

        $this->actingAs($user)
            ->postJson(route('printful-products.customize.store', $product), [
                'printful_variant_id' => $variant->id,
                'color' => 'Black',
                'size' => 'One size',
                'placement' => 'front_dtf_hat',
                'print_data_url' => 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')),
                'preview_data_url' => 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')),
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $uuid = $this->actingAs($user)
            ->postJson(route('printful-products.customize.store', $product), [
                'printful_variant_id' => $variant->id,
                'color' => 'Black',
                'size' => 'One size',
                'placement' => 'front_dtf_hat',
                'print_data_url' => 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')),
            ])
            ->json('data.uuid');

        $this->actingAs($user)
            ->postJson(route('printful-products.customize.add-to-cart', $uuid))
            ->assertOk()
            ->assertJsonPath('success', true);

        $items = app(CartService::class)->all();
        $this->assertGreaterThanOrEqual(2, count($items));
        $this->assertTrue(collect($items)->contains(fn ($item) => ! empty($item['is_customized'])));
    }
}
