<x-app-layout>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">Order {{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <x-status-badge :status="$order->status" class="text-sm" />
        </div>
    </x-slot>

    @php($steps = ['pending', 'processing', 'shipped', 'delivered'])
    @php($current = array_search($order->status->value, $steps, true))

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if ($order->status->value !== 'cancelled')
            <ol class="grid grid-cols-4 gap-2 text-center text-xs font-medium sm:text-sm">
                @foreach ($steps as $i => $step)
                    <li>
                        <div @class(['h-2 rounded-full', 'bg-brand-700' => $current !== false && $i <= $current, 'bg-gray-200' => $current === false || $i > $current])></div>
                        <span @class(['mt-2 block', 'text-brand-800' => $current !== false && $i <= $current, 'text-gray-400' => $current === false || $i > $current])>{{ ucfirst($step) }}</span>
                    </li>
                @endforeach
            </ol>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 overflow-hidden rounded-xl border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3">Product</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3 text-right">Total</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    @if ($item->product)
                                        <a href="{{ route('shop.show', $item->product) }}" class="font-medium hover:text-brand-700">{{ $item->product_name }}</a>
                                    @else
                                        <span class="font-medium">{{ $item->product_name }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ money($item->unit_price) }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">{{ money($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-sm">
                        <tr><td colspan="3" class="px-4 pt-4 text-right text-gray-600">Subtotal</td><td class="px-4 pt-4 text-right">{{ money($order->subtotal) }}</td></tr>
                        <tr><td colspan="3" class="px-4 pt-1 text-right text-gray-600">Delivery</td><td class="px-4 pt-1 text-right">{{ (float) $order->shipping > 0 ? money($order->shipping) : 'Free' }}</td></tr>
                        <tr><td colspan="3" class="px-4 py-3 text-right font-bold">Total</td><td class="px-4 py-3 text-right font-bold">{{ money($order->total) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <aside class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm">
                    <h2 class="mb-2 font-semibold">Delivery address</h2>
                    <p>{{ $order->customer_name }}<br>{{ $order->address_line }}<br>{{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>Phone: {{ $order->phone }}</p>
                    @if ($order->notes)
                        <p class="mt-3 text-gray-600"><span class="font-medium text-gray-800">Notes:</span> {{ $order->notes }}</p>
                    @endif
                    <h2 class="mb-1 mt-4 font-semibold">Payment</h2>
                    <p>Cash on Delivery</p>
                </div>

                @can('cancel', $order)
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">
                        @csrf @method('PATCH')
                        <button class="w-full rounded-lg border border-red-300 bg-white px-5 py-2.5 font-semibold text-red-700 hover:bg-red-50">Cancel order</button>
                    </form>
                @endcan
                <a href="{{ route('orders.index') }}" class="block text-center text-sm text-gray-600 hover:text-gray-900">Back to my orders</a>
            </aside>
        </div>
    </div>
</x-app-layout>
