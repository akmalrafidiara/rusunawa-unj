@if ($showModal && $modalType === 'price')
    <x-managers.ui.modal title="Manajemen Harga Tipe Unit" :show="$showModal && $modalType === 'price'" class="max-w-3xl">
        {{-- Form Harga --}}
        <div class="space-y-6">
            {{-- Unit Type Header --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-lg border border-teal-100">
                <div class="p-3 bg-teal-500 rounded-full">
                    <flux:icon.banknotes class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Harga Tipe Unit: {{ $unitTypeData->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $unitTypeData->description }}</p>
                </div>
            </div>
            {{-- Livewire Component Unit Price --}}
            <livewire:managers.unitPrice :unitType="$unitTypeData" :key="$unitTypeIdBeingEdited" />
        </div>
    </x-managers.ui.modal>
@endif
