<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="mb-6 text-sm text-gray-500">
            <a href="{{ route('shop.index') }}" class="hover:text-gray-800">Shop</a>
            <span class="mx-1">/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-gray-800">{{ $product->category->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover">
            </div>

            <div>
                <p class="text-sm font-medium uppercase tracking-wide text-gray-500">{{ $product->category->name }}</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight">{{ $product->name }}</h1>
                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-bold">{{ money($product->price) }}</span>
                    <x-stock-badge :product="$product" />
                </div>
                <p class="mt-1 text-sm text-gray-500">Inclusive of all taxes</p>

                <div class="prose mt-6 max-w-none text-gray-700">{!! nl2br(e($product->description)) !!}</div>

                @if ($product->inStock())
                    <form method="POST" action="{{ route('cart.store', $product) }}" class="mt-8 flex items-end gap-3">
                        @csrf
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input id="quantity" name="quantity" type="number" min="1" max="{{ min($product->stock, 99) }}" value="1"
                                   class="mt-1 w-24 rounded-lg border-gray-300 focus:border-brand-600 focus:ring-brand-600">
                        </div>
                        <button class="rounded-lg bg-brand-700 px-6 py-2.5 font-semibold text-white hover:bg-brand-800">Add to cart</button>
                    </form>
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                @else
                    <p class="mt-8 rounded-lg bg-gray-100 px-4 py-3 text-gray-600">This product is currently out of stock.</p>
                @endif

                <ul class="mt-8 space-y-2 text-sm text-gray-600">
                    <li>&#10003; Cash on Delivery available</li>
                    <li>&#10003; Free delivery on orders above {{ money(config('shop.free_shipping_threshold')) }}</li>
                    <li>&#10003; 7-day replacement on manufacturing defects</li>
                </ul>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-14">
                <h2 class="mb-5 text-xl font-bold">More in {{ $product->category->name }}</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item->setRelation('category', $product->category)" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
