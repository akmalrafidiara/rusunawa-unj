<div class="flex flex-col gap-6">
    <x-managers.ui.card class="p-6 flex flex-col justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pendapatan</p>
            <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div
            class="mt-4 border-t border-gray-200 dark:border-zinc-700 pt-4 text-xs text-gray-500 dark:text-gray-400 space-y-1">
            <p><span class="font-semibold">Periode:</span>
                @if ($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @else
                    Semua Waktu
                @endif
            </p>
            <p><span class="font-semibold">Filter:</span>
                @if ($filterType === 'monthly')
                    Bulanan
                @elseif ($filterType === 'daily')
                    Harian
                @elseif ($filterType === 'yearly')
                    Tahunan
                @else
                    {{ ucfirst($filterType) }}
                @endif
            </p>
            @if ($occupantFilter)
                <p><span class="font-semibold">Penghuni:</span> {{ $occupantFilter }}</p>
            @endif
            @if ($contractFilter)
                <p><span class="font-semibold">Kontrak:</span> {{ $contractFilter }}</p>
            @endif
        </div>
    </x-managers.ui.card>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-managers.ui.card class="p-6">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rata-Rata / Bulan</p>
            <p class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                Rp{{ number_format($averageMonthlyRevenue, 0, ',', '.') }}</p>
        </x-managers.ui.card>
        <x-managers.ui.card class="p-6">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rata-Rata / Hari</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                Rp{{ number_format($averageDailyRevenue, 0, ',', '.') }}</p>
        </x-managers.ui.card>
    </div>
</div>
