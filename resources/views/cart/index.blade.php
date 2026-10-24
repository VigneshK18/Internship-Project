<x-app-layout>
    <x-slot name="title">Cart</x-slot>
    <x-slot name="header"><h1 class="text-2xl font-bold">Shopping cart</h1></x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($items->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-12 text-center">
                <p class="text-gray-600">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="mt-4 inline-block rounded-lg bg-brand-700 px-5 py-2.5 font-semibold text-white hover:bg-brand-800">Start shopping</a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 divide-y divide-gray-200 rounded-xl border border-gray-200 bg-white">
                    @foreach ($items as $line)
                        @php($product = $line['product'])
                        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                            <img src="{{ $product->image_url }}" alt="" class="h-20 w-24 rounded-lg object-cover">
                            <div class="flex-1">
                                <a href="{{ route('shop.show', $product) }}" class="font-semibold hover:text-brand-700">{{ $product->name }}</a>
                                <p class="text-sm text-gray-500">{{ money($product->price) }} each &middot; {{ $product->stock }} in stock</p>
                            </div>
                            <form method="POST" action="{{ route('cart.update', $product) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <label for="qty-{{ $product->id }}" class="sr-only">Quantity</label>
                                <input id="qty-{{ $product->id }}" name="quantity" type="number" min="0" max="{{ min($product->stock, 99) }}" value="{{ $line['quantity'] }}"
                                       class="w-20 rounded-lg border-gray-300 text-sm focus:border-brand-600 focus:ring-brand-600">
                                <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">Update</button>
                            </form>
                            <div class="w-28 text-right font-semibold">{{ money($line['line_total']) }}</div>
                            <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                @csrf @method('DELETE')
                                <button class="text-sm text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <aside class="h-fit rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="text-lg font-semibold">Order summary</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-600">Subtotal</dt><dd>{{ money($subtotal) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-600">Delivery</dt><dd>{{ $shipping > 0 ? money($shipping) : 'Free' }}</dd></div>
                        <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold"><dt>Total</dt><dd>{{ money($total) }}</dd></div>
                    </dl>
                    @if ($shipping > 0)
                        <p class="mt-3 text-xs text-gray-500">Add {{ money(config('shop.free_shipping_threshold') - $subtotal) }} more for free delivery.</p>
                    @endif
                    <a href="{{ route('checkout.create') }}" class="mt-6 block rounded-lg bg-brand-700 px-5 py-3 text-center font-semibold text-white hover:bg-brand-800">
                        {{ auth()->check() ? 'Proceed to checkout' : 'Log in to checkout' }}
                    </a>
                    <a href="{{ route('shop.index') }}" class="mt-3 block text-center text-sm text-gray-600 hover:text-gray-900">Continue shopping</a>
                </aside>
            </div>
        @endif
    </div>
</x-app-layout>
