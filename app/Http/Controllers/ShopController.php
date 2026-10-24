<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->search($request->query('q'))
            ->inCategory($request->query('category'))
            ->sorted($request->query('sort'))
            ->paginate(config('shop.per_page'))
            ->withQueryString();

        $categories = Category::withCount(['products' => fn ($q) => $q->active()])->orderBy('name')->get();

        return view('shop.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only('q', 'category', 'sort'),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load('category');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
