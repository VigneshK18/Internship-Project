<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_update_and_remove_items(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock' => 10]);

        $this->post(route('cart.store', $product), ['quantity' => 2])->assertSessionHas('success');
        $this->assertSame([$product->id => 2], session('cart'));

        $this->patch(route('cart.update', $product), ['quantity' => 5]);
        $this->assertSame([$product->id => 5], session('cart'));
        $this->get(route('cart.index'))->assertSee('₹2,500.00');

        $this->delete(route('cart.destroy', $product));
        $this->assertSame([], session('cart'));
    }

    public function test_quantity_is_capped_at_available_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->post(route('cart.store', $product), ['quantity' => 5])
            ->assertSessionHas('success', fn ($msg) => str_contains($msg, 'Only 3'));

        $this->assertSame([$product->id => 3], session('cart'));
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->post(route('cart.store', $product))->assertSessionHas('error');
        $this->assertNull(session('cart'));
    }

    public function test_logged_in_cart_is_stored_in_the_database(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)->post(route('cart.store', $product), ['quantity' => 2]);

        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_guest_cart_is_merged_into_the_database_on_login(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store', $product), ['quantity' => 3]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);
        $this->assertNull(session('cart'));
    }

    public function test_free_delivery_threshold_is_applied(): void
    {
        $cheap = Product::factory()->create(['price' => 300]);
        $this->post(route('cart.store', $cheap));
        $this->get(route('cart.index'))->assertSee(money(config('shop.shipping_fee')));

        $this->patch(route('cart.update', $cheap), ['quantity' => 4]); // ₹1,200 >= threshold
        $this->get(route('cart.index'))->assertSee('Free');
    }
}
