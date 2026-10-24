<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

/** Scheduled daily in routes/console.php. */
class CancelStaleOrders extends Command
{
    protected $signature = 'orders:cancel-stale {--hours= : Cancel pending orders older than this many hours}';

    protected $description = 'Cancel pending orders that were never processed and return their items to stock';

    public function handle(OrderService $orders): int
    {
        $hours = (int) ($this->option('hours') ?: config('shop.stale_order_hours'));

        $stale = Order::with('items')
            ->where('status', OrderStatus::Pending)
            ->where('created_at', '<', now()->subHours($hours))
            ->get();

        foreach ($stale as $order) {
            $orders->cancel($order);
            $this->line("Cancelled {$order->order_number}");
        }

        $this->info("{$stale->count()} stale order(s) cancelled.");

        return self::SUCCESS;
    }
}
