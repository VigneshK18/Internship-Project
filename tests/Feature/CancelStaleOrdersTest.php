<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelStaleOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_pending_orders_are_cancelled_and_restocked(): void
    {
        $product = Product::factory()->create(['stock' => 0]);
        $old = Order::factory()->create(['created_at' => now()->subHours(72)]);
        $old->items()->create(['product_id' => $product->id, 'product_name' => 'x', 'unit_price' => 1, 'quantity' => 2, 'line_total' => 2]);
        $recent = Order::factory()->create(['created_at' => now()->subHours(2)]);
        $shipped = Order::factory()->create(['status' => OrderStatus::Shipped, 'created_at' => now()->subDays(10)]);

        $this->artisan('orders:cancel-stale')->expectsOutputToContain('1 stale order(s) cancelled')->assertSuccessful();

        $this->assertSame(OrderStatus::Cancelled, $old->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $recent->fresh()->status);
        $this->assertSame(OrderStatus::Shipped, $shipped->fresh()->status);
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_command_is_scheduled_daily(): void
    {
        $event = collect(app(Schedule::class)->events())->first(fn ($e) => str_contains($e->command, 'orders:cancel-stale'));

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
    }
}
