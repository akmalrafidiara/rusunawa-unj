@props(['options', 'label', 'wireModel', 'isLabel' => true])

@if ($isLabel)
    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
@endif
<select wire:model="{{ $wireModel }}" class="w-full border rounded-md py-2 px-4">
    <option value="">Pilih {{ $label }}</option>
    @foreach ($options as $option)
        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
    @endforeach
</select>
