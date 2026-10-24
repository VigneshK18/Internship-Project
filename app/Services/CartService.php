<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Shopping cart.
 *
 * Guests: product id => quantity is kept in the session.
 * Logged-in customers: rows in the cart_items table, so the cart survives logout.
 * When a guest logs in, the session cart is merged into the database (see MergeSessionCart).
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Lines of the cart with fresh product data.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: float}>
     */
    public function items(): Collection
    {
        $quantities = $this->quantities();
        if ($quantities === []) {
            return collect();
        }

        $products = Product::with('category')->whereIn('id', array_keys($quantities))->active()->get()->keyBy('id');

        return collect($quantities)
            ->filter(fn ($qty, $id) => $products->has($id))
            ->map(function ($qty, $id) use ($products) {
                $product = $products[$id];
                $qty = min($qty, $product->stock);

                return [
                    'product' => $product,
                    'quantity' => $qty,
                    'line_total' => round((float) $product->price * $qty, 2),
                ];
            })
            ->filter(fn ($line) => $line['quantity'] > 0)
            ->values();
    }

    /** Add a quantity of a product, never exceeding the available stock. Returns the new quantity. */
    public function add(Product $product, int $quantity = 1): int
    {
        $current = $this->quantities()[$product->id] ?? 0;

        return $this->set($product, $current + $quantity);
    }

    /** Set the quantity of a product (0 removes it). Returns the stored quantity. */
    public function set(Product $product, int $quantity): int
    {
        $quantity = max(0, min($quantity, $product->stock));

        if ($user = auth()->user()) {
            if ($quantity === 0) {
                $user->cartItems()->where('product_id', $product->id)->delete();
            } else {
                CartItem::updateOrCreate(
                    ['user_id' => $user->id, 'product_id' => $product->id],
                    ['quantity' => $quantity]
                );
            }
        } else {
            $cart = session(self::SESSION_KEY, []);
            if ($quantity === 0) {
                unset($cart[$product->id]);
            } else {
                $cart[$product->id] = $quantity;
            }
            session([self::SESSION_KEY => $cart]);
        }

        return $quantity;
    }

    public function remove(Product $product): void
    {
        $this->set($product, 0);
    }

    public function clear(): void
    {
        if ($user = auth()->user()) {
            $user->cartItems()->delete();
        }
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return (int) array_sum($this->quantities());
    }

    public function isEmpty(): bool
    {
        return $this->quantities() === [];
    }

    public function subtotal(): float
    {
        return round((float) $this->items()->sum('line_total'), 2);
    }

    public function shipping(float $subtotal): float
    {
        if ($subtotal <= 0 || $subtotal >= config('shop.free_shipping_threshold')) {
            return 0.0;
        }

        return (float) config('shop.shipping_fee');
    }

    /** Move the guest's session cart into the database cart of a user who has just logged in. */
    public function mergeSessionInto(User $user): void
    {
        $sessionCart = session(self::SESSION_KEY, []);
        if ($sessionCart === []) {
            return;
        }

        $products = Product::whereIn('id', array_keys($sessionCart))->get()->keyBy('id');
        foreach ($sessionCart as $productId => $qty) {
            if (! $product = $products->get($productId)) {
                continue;
            }
            $item = CartItem::firstOrNew(['user_id' => $user->id, 'product_id' => $productId]);
            $item->quantity = min(($item->quantity ?? 0) + $qty, $product->stock);
            $item->quantity > 0 ? $item->save() : null;
        }
        session()->forget(self::SESSION_KEY);
    }

    /** @return array<int, int> product id => quantity */
    private function quantities(): array
    {
        if ($user = auth()->user()) {
            return $user->cartItems()->pluck('quantity', 'product_id')->map(fn ($q) => (int) $q)->all();
        }

        return array_map('intval', session(self::SESSION_KEY, []));
    }
}
