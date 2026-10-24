<x-app-layout>
    <x-slot name="title">My orders</x-slot>
    <x-slot name="header"><h1 class="text-2xl font-bold">My orders</h1></x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($orders->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center text-gray-600">
                You haven't placed any orders yet. <a href="{{ route('shop.index') }}" class="font-semibold text-brand-700">Browse products</a>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Placed on</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ $order->items_count }}</td>
                                <td class="px-4 py-3">{{ money($order->total) }}</td>
                                <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('orders.show', $order) }}" class="font-semibold text-brand-700 hover:underline">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
</x-app-layout>
