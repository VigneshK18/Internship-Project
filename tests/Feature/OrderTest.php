<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function orderFor(User $user, OrderStatus $status = OrderStatus::Pending, ?Product $product = null, int $qty = 2): Order
    {
        $product ??= Product::factory()->create(['price' => 1000, 'stock' => 10]);
        $order = Order::factory()->for($user)->create(['status' => $status]);
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name,
            'unit_price' => $product->price, 'quantity' => $qty, 'line_total' => $product->price * $qty,
        ]);

        return $order;
    }

    public function test_customer_sees_only_their_own_orders(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $mine = $this->orderFor($me);
        $theirs = $this->orderFor($other);

        $this->actingAs($me)->get(route('orders.index'))
            ->assertSee($mine->order_number)
            ->assertDontSee($theirs->order_number);

        $this->get(route('orders.show', $mine))->assertOk();
        $this->get(route('orders.show', $theirs))->assertForbidden();
    }

    public function test_customer_can_cancel_a_pending_order_and_stock_is_restored(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);
        $order = $this->orderFor($user, OrderStatus::Pending, $product, 2);

        $this->actingAs($user)->patch(route('orders.cancel', $order))->assertRedirect(route('orders.show', $order));

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_shipped_order_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();
        $order = $this->orderFor($user, OrderStatus::Shipped);

        $this->actingAs($user)->patch(route('orders.cancel', $order))->assertForbidden();
        $this->assertSame(OrderStatus::Shipped, $order->fresh()->status);
    }
}
