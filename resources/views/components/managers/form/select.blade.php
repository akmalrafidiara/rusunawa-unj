@props(['options', 'label', 'wireModel', 'isLabel' => true])

<select wire:model="{{ $wireModel }}"
    class="bg-transparant dark:bg-transparent border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full py-2 px-4 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    <option value="" disabled class="dark:bg-zinc-800">Pilih {{ $label }}</option>
    @foreach ($options as $option)
        <option value="{{ $option['value'] }}" class="dark:bg-zinc-800">{{ $option['label'] }}</option>
    @endforeach
</select>
