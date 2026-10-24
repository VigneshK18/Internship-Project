<x-app-layout>
    <x-slot name="title">Shop</x-slot>

    <section class="bg-gradient-to-r from-brand-800 to-brand-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h1 class="text-3xl font-bold tracking-tight">Laptops, audio and accessories</h1>
            <p class="mt-2 max-w-2xl text-brand-100">Genuine products, Cash on Delivery and free delivery on orders above {{ money(config('shop.free_shipping_threshold')) }}.</p>
            <form method="GET" action="{{ route('shop.index') }}" class="mt-6 flex max-w-xl gap-2">
                @if (! empty($filters['category']))
                    <input type="hidden" name="category" value="{{ $filters['category'] }}">
                @endif
                <label for="q" class="sr-only">Search products</label>
                <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="Search products..."
                       class="w-full rounded-lg border-0 px-4 py-2.5 text-gray-900 shadow-sm focus:ring-2 focus:ring-white">
                <button class="rounded-lg bg-white px-5 py-2.5 font-semibold text-brand-800 hover:bg-brand-50">Search</button>
            </form>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col gap-8 lg:flex-row">
            <aside class="lg:w-56 shrink-0">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Categories</h2>
                <nav class="flex flex-wrap gap-2 lg:flex-col lg:gap-1">
                    <a href="{{ route('shop.index', array_filter(['q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                       @class(['rounded-lg px-3 py-2 text-sm', 'bg-brand-50 font-semibold text-brand-800' => empty($filters['category']), 'text-gray-700 hover:bg-gray-100' => ! empty($filters['category'])])>
                        All products
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', array_filter(['category' => $category->slug, 'q' => $filters['q'] ?? null, 'sort' => $filters['sort'] ?? null])) }}"
                           @class(['flex justify-between gap-3 rounded-lg px-3 py-2 text-sm', 'bg-brand-50 font-semibold text-brand-800' => ($filters['category'] ?? null) === $category->slug, 'text-gray-700 hover:bg-gray-100' => ($filters['category'] ?? null) !== $category->slug])>
                            <span>{{ $category->name }}</span>
                            <span class="text-gray-400">{{ $category->products_count }}</span>
                        </a>
                    @endforeach
                </nav>
            </aside>

            <section class="flex-1">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600">
                        {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                        @if (! empty($filters['q'])) for "<strong>{{ $filters['q'] }}</strong>" @endif
                    </p>
                    <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-2">
                        @foreach (['q', 'category'] as $keep)
                            @if (! empty($filters[$keep]))
                                <input type="hidden" name="{{ $keep }}" value="{{ $filters[$keep] }}">
                            @endif
                        @endforeach
                        <label for="sort" class="text-sm text-gray-600">Sort by</label>
                        <select id="sort" name="sort" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
                            @foreach (['' => 'Newest', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['sort'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
                        No products match your search. <a href="{{ route('shop.index') }}" class="font-semibold text-brand-700">Show all products</a>
                    </div>
                @else
                    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $products->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
