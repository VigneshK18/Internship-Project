@php
    $tabs = [
        ['Overview', 'admin.dashboard', 'admin.dashboard'],
        ['Products', 'admin.products.index', 'admin.products.*'],
        ['Categories', 'admin.categories.index', 'admin.categories.*'],
        ['Orders', 'admin.orders.index', 'admin.orders.*'],
    ];
@endphp
<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-2xl font-bold">{{ $heading }}</h1>
    <nav class="flex flex-wrap gap-1 text-sm">
        @foreach ($tabs as [$label, $route, $pattern])
            <a href="{{ route($route) }}" @class([
                'rounded-lg px-3 py-2 font-medium',
                'bg-brand-50 text-brand-800' => request()->routeIs($pattern),
                'text-gray-600 hover:bg-gray-100' => ! request()->routeIs($pattern),
            ])>{{ $label }}</a>
        @endforeach
    </nav>
</div>
