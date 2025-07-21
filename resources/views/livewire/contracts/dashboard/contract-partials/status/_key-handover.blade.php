<div class="p-4 rounded-md bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 flex items-center gap-3">
    <flux:icon.exclamation-circle class="w-6 h-6 flex-shrink-0" />
    <div class="text-sm">
        @php
            $waLink =
                'https://wa.me/625210542017?text=Halo%2C%20saya%20dari%20kontrak%20' .
                $this->contract->contract_code .
                '%20kamar%20' .
                $this->contract->unit->room_number .
                '%20klaster%20' .
                $this->contract->unit->unitCluster->name .
                '%20ingin%20melakukan%20pengembalian%20kunci';
        @endphp
        <p>
            Silakan <a href="{{ $waLink }}" target="_blank"
                class="text-green-800 dark:text-green-200 underline font-semibold">
                Hubungi Pengelola
            </a> untuk pengembalian kunci.
        </p>
    </div>
</div>
