<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index(): View
    {
        $items = $this->cart->items();
        $subtotal = (float) $items->sum('line_total');
        $shipping = $this->cart->shipping($subtotal);

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);
        $data = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:99']]);

        if (! $product->inStock()) {
            return back()->with('error', "{$product->name} is out of stock.");
        }

        $wanted = (int) ($data['quantity'] ?? 1);
        $before = $this->cart->items()->firstWhere('product.id', $product->id)['quantity'] ?? 0;
        $now = $this->cart->add($product, $wanted);

        $message = $now - $before < $wanted
            ? "Only {$product->stock} unit(s) of {$product->name} are available; your cart has the maximum."
            : "{$product->name} added to your cart.";

        return back()->with('success', $message);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:99']]);
        $stored = $this->cart->set($product, (int) $data['quantity']);

        return redirect()->route('cart.index')->with(
            'success',
            $stored < (int) $data['quantity'] ? "Only {$product->stock} unit(s) available." : 'Cart updated.'
        );
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cart->remove($product);

        return redirect()->route('cart.index')->with('success', "{$product->name} removed from your cart.");
    }
}
