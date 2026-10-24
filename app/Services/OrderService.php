<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlaced;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private CartService $cart)
    {
    }

    /**
     * Turn the current cart into an order.
     *
     * Everything runs inside one database transaction: the products are locked, stock is
     * checked and decremented, the order and its items are written and the cart is cleared.
     * If anything fails, nothing is saved.
     *
     * @param array<string, string|null> $details validated checkout form data
     *
     * @throws InsufficientStockException
     */
    public function placeOrder(User $user, array $details): Order
    {
        $lines = $this->cart->items();
        if ($lines->isEmpty()) {
            throw new \LogicException('The cart is empty.');
        }

        $order = DB::transaction(function () use ($user, $details, $lines) {
            // lock the rows so two customers cannot buy the last unit at the same time
            $products = Product::whereIn('id', $lines->pluck('product.id'))->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            foreach ($lines as $line) {
                $product = $products[$line['product']->id];
                if ($product->stock < $line['quantity']) {
                    throw InsufficientStockException::for($product->name, $product->stock);
                }
                $subtotal += (float) $product->price * $line['quantity'];
            }
            $subtotal = round($subtotal, 2);
            $shipping = $this->cart->shipping($subtotal);

            $order = $user->orders()->create([
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
                'customer_name' => $details['customer_name'],
                'phone' => $details['phone'],
                'address_line' => $details['address_line'],
                'city' => $details['city'],
                'state' => $details['state'],
                'postal_code' => $details['postal_code'],
                'payment_method' => $details['payment_method'] ?? 'cod',
                'notes' => $details['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $product = $products[$line['product']->id];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $line['quantity'],
                    'line_total' => round((float) $product->price * $line['quantity'], 2),
                ]);
                $product->decrement('stock', $line['quantity']);
            }

            $this->cart->clear();

            return $order;
        });

        // queued: the customer gets the confirmation without waiting for the mail to be sent
        $user->notify(new OrderPlaced($order));

        return $order->load('items');
    }

    /** Cancel an order and put its items back into stock. */
    public function cancel(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }
            $order->update(['status' => OrderStatus::Cancelled]);
        });
    }
}
