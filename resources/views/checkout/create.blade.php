<x-app-layout>
    <x-slot name="title">Checkout</x-slot>
    <x-slot name="header"><h1 class="text-2xl font-bold">Checkout</h1></x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('checkout.store') }}" class="grid gap-8 lg:grid-cols-3">
            @csrf
            <div class="lg:col-span-2 space-y-6 rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-lg font-semibold">Delivery address</h2>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="customer_name" value="Full name" />
                        <x-text-input id="customer_name" name="customer_name" class="mt-1 block w-full" required
                                      :value="old('customer_name', $last->customer_name ?? auth()->user()->name)" />
                        <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Mobile number" />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" required inputmode="numeric" placeholder="9876543210"
                                      :value="old('phone', $last->phone ?? '')" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="address_line" value="Address (house no., street, area)" />
                        <x-text-input id="address_line" name="address_line" class="mt-1 block w-full" required
                                      :value="old('address_line', $last->address_line ?? '')" />
                        <x-input-error :messages="$errors->get('address_line')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="city" value="City" />
                        <x-text-input id="city" name="city" class="mt-1 block w-full" required :value="old('city', $last->city ?? '')" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="state" value="State" />
                        <x-text-input id="state" name="state" class="mt-1 block w-full" required :value="old('state', $last->state ?? '')" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="postal_code" value="PIN code" />
                        <x-text-input id="postal_code" name="postal_code" class="mt-1 block w-full" required inputmode="numeric" maxlength="6"
                                      :value="old('postal_code', $last->postal_code ?? '')" />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="notes" value="Delivery notes (optional)" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>
                </div>

                <h2 class="pt-2 text-lg font-semibold">Payment</h2>
                <label class="flex items-center gap-3 rounded-lg border border-brand-200 bg-brand-50 p-4">
                    <input type="radio" name="payment_method" value="cod" checked class="text-brand-700 focus:ring-brand-600">
                    <span><span class="font-semibold">Cash on Delivery</span><span class="block text-sm text-gray-600">Pay when your order arrives.</span></span>
                </label>
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
            </div>

            <aside class="h-fit rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-lg font-semibold">Your order</h2>
                <ul class="mt-4 divide-y divide-gray-100 text-sm">
                    @foreach ($items as $line)
                        <li class="flex justify-between gap-3 py-2">
                            <span>{{ $line['quantity'] }} &times; {{ $line['product']->name }}</span>
                            <span class="whitespace-nowrap">{{ money($line['line_total']) }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 space-y-2 border-t border-gray-200 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-600">Subtotal</dt><dd>{{ money($subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">Delivery</dt><dd>{{ $shipping > 0 ? money($shipping) : 'Free' }}</dd></div>
                    <div class="flex justify-between pt-2 text-base font-bold"><dt>Total</dt><dd>{{ money($total) }}</dd></div>
                </dl>
                <button class="mt-6 w-full rounded-lg bg-brand-700 px-5 py-3 font-semibold text-white hover:bg-brand-800">Place order</button>
                <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-sm text-gray-600 hover:text-gray-900">Back to cart</a>
            </aside>
        </form>
    </div>
</x-app-layout>
