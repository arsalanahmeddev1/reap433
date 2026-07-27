<?php

namespace Tests\Feature;

use App\Models\FavouriteProduct;
use App\Models\PrintfulProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavouriteProductTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $overrides = []): PrintfulProduct
    {
        return PrintfulProduct::query()->create(array_merge([
            'printful_product_id' => fake()->unique()->numberBetween(1000, 999999),
            'name' => 'Favourite Test Product',
            'is_synced' => true,
        ], $overrides));
    }

    public function test_guest_cannot_add_favourite_and_is_redirected_to_login(): void
    {
        $product = $this->makeProduct();

        $this->post(route('favourites.store', $product))
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('favourite_products', 0);
    }

    public function test_authenticated_user_can_add_favourite(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();

        $this->actingAs($user)
            ->postJson(route('favourites.store', $product))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_favourite', true);

        $this->assertDatabaseHas('favourite_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_duplicate_favourite_is_not_created(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();

        FavouriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('favourites.store', $product))
            ->assertOk()
            ->assertJsonPath('message', 'Product is already in your favourites.');

        $this->assertSame(1, FavouriteProduct::withTrashed()->where('user_id', $user->id)->where('product_id', $product->id)->count());
    }

    public function test_soft_deleted_favourite_is_restored_when_added_again(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();

        $favourite = FavouriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $favourite->delete();

        $this->actingAs($user)
            ->postJson(route('favourites.store', $product))
            ->assertOk()
            ->assertJsonPath('is_favourite', true);

        $this->assertFalse($favourite->fresh()->trashed());
        $this->assertSame(1, FavouriteProduct::withTrashed()->where('user_id', $user->id)->where('product_id', $product->id)->count());
    }

    public function test_user_can_remove_their_own_favourite(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();

        FavouriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('favourites.destroy', $product))
            ->assertOk()
            ->assertJsonPath('is_favourite', false);

        $this->assertSoftDeleted('favourite_products', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_cannot_remove_another_users_favourite(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $product = $this->makeProduct();

        FavouriteProduct::create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($other)
            ->deleteJson(route('favourites.destroy', $product))
            ->assertNotFound();

        $this->assertDatabaseHas('favourite_products', [
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_dashboard_only_displays_logged_in_users_favourites(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ownProduct = $this->makeProduct(['name' => 'Mine']);
        $otherProduct = $this->makeProduct(['name' => 'Theirs']);

        FavouriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $ownProduct->id,
        ]);
        FavouriteProduct::create([
            'user_id' => $other->id,
            'product_id' => $otherProduct->id,
        ]);

        $this->actingAs($user)
            ->get(route('favourites.index'))
            ->assertOk()
            ->assertSee('Mine')
            ->assertDontSee('Theirs');
    }

    public function test_invalid_product_id_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/products/999999/favourite')
            ->assertNotFound();
    }

    public function test_favourite_status_is_returned_on_product_listings(): void
    {
        $user = User::factory()->create();
        $favourited = $this->makeProduct(['name' => 'Favourited Cap']);
        $plain = $this->makeProduct(['name' => 'Plain Cap']);

        FavouriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $favourited->id,
        ]);

        $this->actingAs($user)
            ->get(route('printful-products.index'))
            ->assertOk()
            ->assertSee('data-product-id="'.$favourited->id.'"', false)
            ->assertSee('is-favourite', false)
            ->assertSee('data-product-id="'.$plain->id.'"', false);
    }

    public function test_toggle_adds_and_removes_favourite(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();

        $this->actingAs($user)
            ->postJson(route('favourites.toggle', $product))
            ->assertOk()
            ->assertJsonPath('is_favourite', true);

        $this->actingAs($user)
            ->postJson(route('favourites.toggle', $product))
            ->assertOk()
            ->assertJsonPath('is_favourite', false);
    }
}
