<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo data:
     *   admin@techkart.test    / password   (administrator)
     *   customer@techkart.test / password   (customer with a few past orders)
     */
    public function run(): void
    {
        $this->call(CatalogueSeeder::class);

        $admin = User::firstOrNew(['email' => 'admin@techkart.test']);
        $admin->forceFill(['name' => 'Store Admin', 'password' => 'password', 'is_admin' => true, 'email_verified_at' => now()])->save();

        $customer = User::firstOrNew(['email' => 'customer@techkart.test']);
        $customer->forceFill(['name' => 'Demo Customer', 'password' => 'password', 'email_verified_at' => now()])->save();

        if ($customer->orders()->exists()) {
            return;
        }

        $samples = [
            [OrderStatus::Delivered, 20, ['wireless-mouse' => 1, 'laptop-backpack-156' => 1]],
            [OrderStatus::Shipped, 4, ['true-wireless-earbuds' => 2]],
            [OrderStatus::Pending, 0, ['128-gb-usb-32-pen-drive' => 1, '1-tb-portable-ssd' => 1]],
        ];

        foreach ($samples as [$status, $daysAgo, $lines]) {
            $products = Product::whereIn('slug', array_keys($lines))->get();
            if ($products->isEmpty()) {
                continue;
            }
            $subtotal = $products->sum(fn ($p) => $p->price * $lines[$p->slug]);
            $shipping = $subtotal >= config('shop.free_shipping_threshold') ? 0 : config('shop.shipping_fee');

            $order = new Order([
                'status' => $status,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
                'customer_name' => 'Demo Customer',
                'phone' => '9876543210',
                'address_line' => '12-3, Sree Sainath Nagar, A. Rangampet',
                'city' => 'Tirupati',
                'state' => 'Andhra Pradesh',
                'postal_code' => '517102',
                'payment_method' => 'cod',
            ]);
            $order->user()->associate($customer);
            $order->created_at = now()->subDays($daysAgo);
            $order->save();

            foreach ($products as $product) {
                $qty = $lines[$product->slug];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'line_total' => $product->price * $qty,
                ]);
            }
        }
    }
}
