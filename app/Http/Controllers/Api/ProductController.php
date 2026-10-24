<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /** GET /api/products?q=&category=&sort=&per_page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 12), 1), 50);

        return ProductResource::collection(
            Product::with('category')
                ->active()
                ->search($request->query('q'))
                ->inCategory($request->query('category'))
                ->sorted($request->query('sort'))
                ->paginate($perPage)
                ->withQueryString()
        );
    }

    /** GET /api/products/{slug} */
    public function show(Product $product): ProductResource
    {
        abort_unless($product->is_active, 404);

        return new ProductResource($product->load('category'));
    }
}
