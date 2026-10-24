<x-app-layout>
    <x-slot name="title">New product</x-slot>
    <x-slot name="header">@include('admin._nav', ['heading' => 'New product'])</x-slot>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6">
            @csrf
            @include('admin.products._form')
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm">Cancel</a>
                <button class="rounded-lg bg-brand-700 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-800">Create product</button>
            </div>
        </form>
    </div>
</x-app-layout>
