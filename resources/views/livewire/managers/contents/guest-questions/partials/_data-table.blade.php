<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama Lengkap', 'Status', 'Telepon', 'Email', 'Pesan', 'Aksi']"
        :headerWidths="['w-1/4', 'w-auto', 'w-auto', 'w-2/5', 'w-auto', 'w-auto']">
        <x-managers.table.body>
            @forelse ($guestQuestions as $question)
                @php
                    $phoneNumberRaw = $question->formPhoneNumber;
                    $whatsappLink = null;
                    if ($phoneNumberRaw) {
                        $cleanPhoneNumber = preg_replace('/[^0-9]/', '', $phoneNumberRaw);
                        if ($cleanPhoneNumber) {
                            if (substr($cleanPhoneNumber, 0, 1) === '0') {
                                $whatsappNumber = '62' . substr($cleanPhoneNumber, 1);
                            } elseif (substr($cleanPhoneNumber, 0, 2) === '62') {
                                $whatsappNumber = $cleanPhoneNumber;
                            } else {
                                $whatsappNumber = '62' . $cleanPhoneNumber;
                            }
                            $whatsappLink = "https://wa.me/{$whatsappNumber}";
                        }
                    }

                    $emailLink = null;
                    if ($question->formEmail) {
                        $emailLink = "mailto:{$question->formEmail}";
                    }
                @endphp

                <x-managers.table.row wire:key="{{ $question->id }}"
                    class="@if (!$question->is_read) bg-blue-50 @endif hover:bg-gray-100 transition duration-150 ease-in-out">
                    <x-managers.table.cell class="w-1/4">
                        <span class="font-bold" style="word-break: break-word;">{{ $question->fullName }}</span>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-1/5">
                        @if ($question->is_read)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sudah Dibaca
                            </span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-yellow-800">
                                Belum Dibaca
                            </span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-auto">
                        @if ($whatsappLink)
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center text-sm text-gray-700 hover:text-green-600 transition duration-150 ease-in-out">
                                <flux:icon.phone class="w-5 h-5 mr-1 text-green-500" />
                                {{ $question->formPhoneNumber ?? '-' }}
                            </a>
                        @else
                            <span class="text-sm text-gray-700">{{ $question->formPhoneNumber ?? '-' }}</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-auto">
                        @if ($emailLink)
                            <a href="{{ $emailLink }}"
                                class="flex items-center text-sm text-gray-700 hover:text-blue-600 transition duration-150 ease-in-out">
                                <flux:icon.envelope class="w-5 h-5 mr-1 text-blue-500" />
                                {{ $question->formEmail ?? '-' }}
                            </a>
                        @else
                            <span class="text-sm text-gray-700">{{ $question->formEmail ?? '-' }}</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-3/5">
                        <span class="text-sm text-gray-700" style="word-break: break-word;">
                            {{ $question->message }}
                        </span>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-2/5 text-right">
                        <div class="flex items-center space-x-2">
                            @if (!$question->is_read)
                                <button wire:click="confirmMarkAsRead({{ $question->id }})"
                                    class="text-green-600 hover:text-green-900" title="Tandai Sudah Dibaca">
                                    <x-icon.check class="w-5 h-5" />
                                </button>
                            @endif
                            <button wire:click="confirmDeleteQuestion({{ $question->id }})"
                                class="text-red-600 hover:text-red-900" title="Hapus">
                                <x-icon.trash class="w-5 h-5" />
                            </button>
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        Tidak ada pertanyaan tamu yang tersedia.
                    </td>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    <x-managers.ui.pagination :paginator="$guestQuestions" />
</x-managers.ui.card>