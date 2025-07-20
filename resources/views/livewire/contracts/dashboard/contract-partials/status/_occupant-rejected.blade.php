<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 shadow-sm text-center">
    <div class="flex flex-col space-y-4 justify-center items-center">
        <flux:icon name="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400" />
        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Ditolak</h4>
        <ul class="space-y-2 text-left bg-red-100 dark:bg-red-900/30 p-5 rounded w-full">
            @foreach ($occupants as $occupant)
                @if ($occupant->status === \App\Enums\OccupantStatus::REJECTED)
                    <li class="text-gray-700 dark:text-gray-300">
                        <div class="flex flex-col">
                            <h4 class="font-semibold">{{ $occupant->full_name }} | <a
                                    wire:click="showOccupantForm({{ $occupant->id }})"
                                    class="text-green-600 hover:underline">Edit</a></h4>
                            <table>
                                <tr class="text-sm">
                                    <td class="align-top w-8">Alasan:</td>
                                    <td class="pl-2 align-top">
                                        {{ $occupant->verificationLogs->last()->reason ?? 'Alasan tidak tersedia.' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Anda dapat mengajukan permohonan ulang setelah memperbaiki data yang diperlukan.
        </p>
    </div>
</div>
