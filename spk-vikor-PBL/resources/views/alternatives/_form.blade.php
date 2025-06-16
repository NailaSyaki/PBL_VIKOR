@csrf
<div class="mb-4">
    <x-input-label for="name" :value="__('Nama Alternatif')" />
    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $alternative->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="description" :value="__('Deskripsi (Opsional)')" />
    <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $alternative->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<h3 class="text-lg font-medium text-gray-900 mt-6 mb-2">Nilai Kriteria:</h3>
@foreach ($criteria as $criterion)
    <div class="mb-4">
        <x-input-label :for="'criteria[' . $criterion->id . ']'" :value="$criterion->name . ' (' . $criterion->code . ' - ' . $criterion->type . ')'" />
        <x-text-input :id="'criteria[' . $criterion->id . ']'" class="block mt-1 w-full" type="number" step="any" :name="'criteria[' . $criterion->id . ']'" :value="old('criteria.' . $criterion->id, $alternative_evaluations[$criterion->id] ?? '')" required />
        <x-input-error :messages="$errors->get('criteria.' . $criterion->id)" class="mt-2" />
    </div>
@endforeach

<div class="flex items-center justify-end mt-4">
    <x-primary-button class="ml-4">
        {{ __('Simpan') }}
    </x-primary-button>
</div>