<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" required :value="old('name', $category->name)" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <x-input-label for="description" value="Description (optional)" />
    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('description', $category->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
