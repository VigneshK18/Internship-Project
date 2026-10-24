<x-app-layout>
    <x-slot name="title">My account</x-slot>
    <x-slot name="header"><h1 class="text-2xl font-bold">Hello, {{ auth()->user()->name }}</h1></x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <div class="grid gap-5 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-6"><p class="text-sm text-gray-500">Orders placed</p><p class="mt-1 text-3xl font-bold">{{ $orderCount }}</p></div>
            <div class="rounded-xl border border-gray-200 bg-white p-6"><p class="text-sm text-gray-500">Total spent</p><p class="mt-1 text-3xl font-bold">{{ money($spent) }}</p></div>
            <div class="rounded-xl border border-gray-200 bg-white p-6"><p class="text-sm text-gray-500">Member since</p><p class="mt-1 text-3xl font-bold">{{ auth()->user()->created_at->format('M Y') }}</p></div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="font-semibold">Recent orders</h2>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>
            @forelse ($recentOrders as $order)
                <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50 @if (! $loop->last) border-b border-gray-100 @endif">
                    <div><p class="font-medium">{{ $order->order_number }}</p><p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items_count }} item(s)</p></div>
                    <div class="flex items-center gap-4"><span class="font-semibold">{{ money($order->total) }}</span><x-status-badge :status="$order->status" /></div>
                </a>
            @empty
                <p class="px-6 py-8 text-center text-gray-500">No orders yet. <a href="{{ route('shop.index') }}" class="font-semibold text-brand-700">Start shopping</a></p>
            @endforelse
        </div>
    </div>
</x-app-layout>
