<x-app-layout>
    <x-slot name="title">Admin</x-slot>
    <x-slot name="header">@include('admin._nav', ['heading' => 'Admin overview'])</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($stats as $label => $value)
                <div class="rounded-xl border border-gray-200 bg-white p-5">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h2 class="font-semibold">Recent orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">All orders</a>
                </div>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="px-6 py-3"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold hover:text-brand-700">{{ $order->order_number }}</a></td>
                                <td class="px-6 py-3 text-gray-600">{{ $order->customer_name }}</td>
                                <td class="px-6 py-3">{{ money($order->total) }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$order->status" /></td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-8 text-center text-gray-500">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4"><h2 class="font-semibold">Low stock</h2></div>
                <ul class="divide-y divide-gray-100 text-sm">
                    @forelse ($lowStock as $product)
                        <li class="flex items-center justify-between px-6 py-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="hover:text-brand-700">{{ $product->name }}</a>
                            <span @class(['font-semibold', 'text-red-600' => $product->stock === 0, 'text-amber-700' => $product->stock > 0])>{{ $product->stock }}</span>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-gray-500">All products are well stocked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
