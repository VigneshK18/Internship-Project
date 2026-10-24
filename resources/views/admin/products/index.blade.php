<x-app-layout>
    <x-slot name="title">Products</x-slot>
    <x-slot name="header">@include('admin._nav', ['heading' => 'Products'])</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" class="flex gap-2">
                <input name="q" type="search" value="{{ request('q') }}" placeholder="Search products" class="rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
                <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm hover:bg-gray-50">Search</button>
            </form>
            <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-brand-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-brand-800">+ New product</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Stock</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="" class="h-10 w-12 rounded object-cover">
                                    <span class="font-medium">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $product->category->name }}</td>
                            <td class="px-4 py-3">{{ money($product->price) }}</td>
                            <td class="px-4 py-3"><span @class(['font-semibold', 'text-red-600' => $product->stock === 0, 'text-amber-700' => $product->stock > 0 && $product->stock < 5])>{{ $product->stock }}</span></td>
                            <td class="px-4 py-3">
                                @if ($product->is_active)
                                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Visible</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Hidden</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="ms-3 inline" onsubmit="return confirm('Delete {{ $product->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </div>
</x-app-layout>
