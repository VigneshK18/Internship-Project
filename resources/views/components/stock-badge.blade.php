@props(['product'])
@if ($product->stock === 0)
    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Out of stock</span>
@elseif ($product->stock < 5)
    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Only {{ $product->stock }} left</span>
@else
    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">In stock</span>
@endif
