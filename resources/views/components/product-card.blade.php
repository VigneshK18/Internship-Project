@props(['product'])
<div class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white transition hover:shadow-md">
    <a href="{{ route('shop.show', $product) }}" class="block aspect-[4/3] overflow-hidden bg-gray-100">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col gap-2 p-4">
        <span class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $product->category->name }}</span>
        <a href="{{ route('shop.show', $product) }}" class="font-semibold leading-snug text-gray-900 hover:text-brand-700">{{ $product->name }}</a>
        <div class="mt-auto flex items-center justify-between pt-2">
            <span class="text-lg font-bold">{{ money($product->price) }}</span>
            <x-stock-badge :product="$product" />
        </div>
        <form method="POST" action="{{ route('cart.store', $product) }}" class="pt-1">
            @csrf
            <button type="submit" @disabled(! $product->inStock())
                    class="w-full rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 disabled:cursor-not-allowed disabled:bg-gray-300">
                {{ $product->inStock() ? 'Add to cart' : 'Out of stock' }}
            </button>
        </form>
    </div>
</div>
