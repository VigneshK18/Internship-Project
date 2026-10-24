<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function address(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Vignesh Kadiyala',
            'phone' => '9876543210',
            'address_line' => 'Sree Sainath Nagar, A. Rangampet',
            'city' => 'Tirupati',
            'state' => 'Andhra Pradesh',
            'postal_code' => '517102',
            'payment_method' => 'cod',
        ], $overrides);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('checkout.create'))->assertRedirect(route('login'));
    }

    public function test_empty_cart_cannot_be_checked_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('checkout.create'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_customer_can_place_an_order(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $mouse = Product::factory()->create(['name' => 'Mouse', 'price' => 800, 'stock' => 10]);
        $ssd = Product::factory()->create(['name' => 'SSD', 'price' => 7000, 'stock' => 2]);

        $this->actingAs($user)->post(route('cart.store', $mouse), ['quantity' => 2]);
        $this->post(route('cart.store', $ssd));

        $response = $this->post(route('checkout.store'), $this->address());

        $order = Order::with('items')->sole();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertEquals(8600, (float) $order->subtotal);
        $this->assertEquals(0, (float) $order->shipping);          // above the free-delivery threshold
        $this->assertEquals(8600, (float) $order->total);
        $this->assertCount(2, $order->items);

        // stock decremented and cart emptied
        $this->assertSame(8, $mouse->fresh()->stock);
        $this->assertSame(1, $ssd->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);

        Notification::assertSentTo($user, OrderPlaced::class, fn ($n) => $n->order->is($order));
    }

    public function test_order_confirmation_is_queued(): void
    {
        $this->assertContains(\Illuminate\Contracts\Queue\ShouldQueue::class, class_implements(OrderPlaced::class));
    }

    public function test_checkout_is_validated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $this->actingAs($user)->post(route('cart.store', $product));

        $this->post(route('checkout.store'), $this->address(['phone' => '12345', 'postal_code' => 'abc']))
            ->assertSessionHasErrors(['phone', 'postal_code']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_is_refused_and_nothing_saved_when_stock_ran_out(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'price' => 100]);

        // the cart still believes 5 units are available...
        $stale = (clone $product)->forceFill(['stock' => 5]);
        $this->partialMock(CartService::class, function ($mock) use ($stale) {
            $mock->shouldReceive('items')->andReturn(collect([['product' => $stale, 'quantity' => 5, 'line_total' => 500.0]]));
            $mock->shouldReceive('isEmpty')->andReturn(false);
        });
        // ...but another customer bought 3 of them a moment ago
        Product::whereKey($product->id)->update(['stock' => 2]);

        $this->actingAs($user)->post(route('checkout.store'), $this->address())
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error', fn ($msg) => str_contains($msg, 'Only 2'));

        // the transaction rolled back: no order, no items, stock untouched
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(2, $product->fresh()->stock);
    }
}
