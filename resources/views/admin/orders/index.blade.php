<x-app-layout>
    <x-slot name="title">Orders</x-slot>
    <x-slot name="header">@include('admin._nav', ['heading' => 'Orders'])</x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="GET" class="mb-5 flex flex-wrap gap-2">
            <input name="q" type="search" value="{{ request('q') }}" placeholder="Order no. or customer" class="rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
            <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
                <option value="">All statuses</option>
                @foreach (\App\Enums\OrderStatus::options() as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm hover:bg-gray-50">Filter</button>
        </form>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Items</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->customer_name }}<span class="block text-xs text-gray-500">{{ $order->user->email }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $order->items_count }}</td>
                            <td class="px-4 py-3">{{ money($order->total) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-brand-700 hover:underline">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
