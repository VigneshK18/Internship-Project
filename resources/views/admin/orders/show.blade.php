<x-app-layout>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>
    <x-slot name="header">@include('admin._nav', ['heading' => 'Order '.$order->order_number])</x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 overflow-hidden rounded-xl border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3 text-right">Total</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $item->product_name }}</td>
                                <td class="px-4 py-3">{{ money($item->unit_price) }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">{{ money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3" class="px-4 pt-4 text-right text-gray-600">Subtotal</td><td class="px-4 pt-4 text-right">{{ money($order->subtotal) }}</td></tr>
                        <tr><td colspan="3" class="px-4 pt-1 text-right text-gray-600">Delivery</td><td class="px-4 pt-1 text-right">{{ (float) $order->shipping > 0 ? money($order->shipping) : 'Free' }}</td></tr>
                        <tr><td colspan="3" class="px-4 py-3 text-right font-bold">Total</td><td class="px-4 py-3 text-right font-bold">{{ money($order->total) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <aside class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="mb-4 flex items-center justify-between"><h2 class="font-semibold">Status</h2><x-status-badge :status="$order->status" /></div>
                    @if ($order->status->value !== 'cancelled')
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
                            @csrf @method('PATCH')
                            <select name="status" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
                                @foreach (\App\Enums\OrderStatus::options() as $value => $label)
                                    <option value="{{ $value }}" @selected($order->status->value === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Update</button>
                        </form>
                        <p class="mt-2 text-xs text-gray-500">Cancelling returns the items to stock.</p>
                    @else
                        <p class="text-sm text-gray-500">This order was cancelled.</p>
                    @endif
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm">
                    <h2 class="mb-2 font-semibold">Customer</h2>
                    <p>{{ $order->customer_name }}<br>{{ $order->user->email }}<br>{{ $order->phone }}</p>
                    <h2 class="mb-2 mt-4 font-semibold">Delivery address</h2>
                    <p>{{ $order->address_line }}<br>{{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}</p>
                    @if ($order->notes)<p class="mt-3 text-gray-600">Notes: {{ $order->notes }}</p>@endif
                    <p class="mt-4 text-gray-500">Placed {{ $order->created_at->format('d M Y, h:i A') }} &middot; Cash on Delivery</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="block text-center text-sm text-gray-600 hover:text-gray-900">Back to orders</a>
            </aside>
        </div>
    </div>
</x-app-layout>
