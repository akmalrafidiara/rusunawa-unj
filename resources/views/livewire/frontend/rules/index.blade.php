<?php

use function Livewire\Volt\{state, mount};
use App\Models\Regulation;

state([
    'regulations' => [],
]);

mount(function () {
    $this->regulations = Regulation::orderBy('priority', 'asc')->get();
});

// Helper function to convert integer to Roman numeral
function toRoman($num) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($num > 0) {
        foreach ($map as $roman => $int) {
            if ($num >= $int) {
                $num -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

?>

<div class="relative w-full py-0 px-6 sm:px-6 lg:px-10 mb-5 lg:mb-15 overflow-hidden">

    {{-- Tampilan untuk MOBILE --}}
    <div class="lg:hidden">
        <div class="container mx-auto px-4">
            <div class="space-y-6">
                @if ($regulations->isNotEmpty())
                @foreach ($regulations as $regulation)
                <div class="pb-4 mb-4 border-b border-gray-300 dark:border-zinc-700">
                    {{-- Judul BAB di Mobile --}}
                    <div class="mb-2">
                        {{-- Changed priority to Roman numeral here --}}
                        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">BAB {{ toRoman($regulation->priority) }}</div>
                        <div class="text-l font-bold text-gray-800 dark:text-gray-200">{{ $regulation->title }}</div>
                    </div>
                    {{-- Isi BAB di Mobile --}}
                    <div class="text-gray-700 dark:text-gray-300 leading-relaxed text-sm trix-content">
                        {!! $regulation->content !!}
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-center text-gray-600 dark:text-gray-400 py-10">Belum ada tata tertib yang tersedia saat ini.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Tampilan untuk DESKTOP --}}
    <div class="hidden lg:block">
        <div class="container mx-auto">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <tbody>
                        @if ($regulations->isNotEmpty())
                        @foreach ($regulations as $regulation)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 align-top w-1/4">
                                {{-- Changed priority to Roman numeral here --}}
                                <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">BAB {{ toRoman($regulation->priority) }}</div>
                                <div class="text-xl font-bold text-gray-900 dark:text-gray-200">{{ $regulation->title }}</div>
                            </td>
                            <td class="px-6 py-4 align-top w-3/4">
                                <div class="text-base text-gray-700 dark:text-gray-300 leading-relaxed trix-content">
                                    {!! $regulation->content !!}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                                Belum ada tata tertib yang tersedia saat ini.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>