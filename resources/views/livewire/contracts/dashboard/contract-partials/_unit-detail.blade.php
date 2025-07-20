<div class="bg-white dark:bg-zinc-800 rounded-lg border dark:border-zinc-700 shadow-md flex items-center">
    <img src="{{ $unit->unitType->attachments()->first() ? asset('storage/' . $unit->unitType->attachments()->first()->path) : asset('images/placeholder.png') }}"
        alt="Unit Image" class="w-1/3 h-24 object-cover rounded-l-lg">
    <div class="p-4">
        <h2 class="text-xl font-semibold">Unit {{ $unit->room_number }} | {{ $unit->unitCluster->name }}</h2>
        <p class="text-gray-600 dark:text-gray-400">{{ $contract->unit->unitType->name }} |
            {{ $contract->occupantType->name }}
        </p>
    </div>
</div>
