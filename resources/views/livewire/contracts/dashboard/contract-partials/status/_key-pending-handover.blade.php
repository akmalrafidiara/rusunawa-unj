<div
    class="p-4 rounded-md bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 flex items-center gap-3">
    <flux:icon.exclamation-circle class="w-6 h-6 flex-shrink-0" />
    <div>
        @php
            $waLink =
                'https://wa.me/625210542017?text=Halo%2C%20saya%20dari%20kontrak%20' .
                $this->contract->contract_code .
                '%20kamar%20' .
                $this->contract->unit->room_number .
                '%20klaster%20' .
                $this->contract->unit->unitCluster->name .
                '%20ingin%20melakukan%20serah%20terima%20kunci';
        @endphp
        <strong>Kunci Belum Diserahkan</strong>
        <p>
            Mohon segera <a href="{{ $waLink }}" target="_blank"
                class=" text-green-800 dark:text-green-200 underline">
                Hubungi Pengelola
            </a> untuk serah terima kunci.
        </p>
    </div>
</div>
