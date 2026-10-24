<div class="grid gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="name" value="Product name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" required :value="old('name', $product->name)" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="category_id" value="Category" />
        <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">
            <option value="">Choose a category</option>
            @foreach ($categories as $id => $name)
                <option value="{{ $id }}" @selected((string) old('category_id', $product->category_id) === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="price" value="Price (₹)" />
            <x-text-input id="price" name="price" type="number" step="0.01" min="1" class="mt-1 block w-full" required :value="old('price', $product->price)" />
            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="stock" value="Stock" />
            <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" required :value="old('stock', $product->stock)" />
            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
        </div>
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="description" value="Description" />
        <textarea id="description" name="description" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('description', $product->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="image" value="Image (JPG, PNG or WebP, max 2 MB)" />
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-600 file:me-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium">
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>
    <div class="flex items-center gap-4">
        @if ($product->image)
            <img src="{{ $product->image_url }}" alt="" class="h-20 w-24 rounded-lg object-cover">
        @endif
        <label class="flex items-center gap-2 text-sm">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-gray-300 text-brand-700 focus:ring-brand-600">
            Visible in the shop
        </label>
    </div>
</div>
