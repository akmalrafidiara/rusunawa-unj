<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nama Lengkap', 'Status', 'Telepon', 'Email', 'Pesan', 'Aksi']"
        :headerWidths="['w-1/4', 'w-auto', 'w-auto', 'w-2/5', 'w-auto', 'w-auto']">
        <x-managers.table.body>
            @forelse ($guestQuestions as $question)
                <x-managers.table.row wire:key="{{ $question->id }}">
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
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Belum Dibaca
                            </span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-auto">
                        {{-- Menggunakan fungsi dari Livewire untuk link WhatsApp dengan 3 argumen --}}
                        @php
                            $whatsappLink = $this->getWhatsappLink($question->formPhoneNumber, $question->message, $question->created_at);
                        @endphp
                        @if ($whatsappLink)
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center text-sm text-gray-700 dark:text-gray-200 hover:text-green-600 transition duration-150 ease-in-out">
                                <flux:icon.phone class="w-5 h-5 mr-1 text-green-500" />
                                {{ $question->formPhoneNumber ?? '-' }}
                            </a>
                        @else
                            <span class="text-sm text-gray-700">{{ $question->formPhoneNumber ?? '-' }}</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-auto">
                        {{-- Menggunakan fungsi dari Livewire untuk link Email dengan 3 argumen --}}
                        @php
                            $emailLink = $this->getEmailLink($question->formEmail, $question->message, $question->created_at);
                        @endphp
                        @if ($emailLink)
                            <a href="{{ $emailLink }}"
                                class="flex items-center text-sm text-gray-700 dark:text-gray-200 hover:text-blue-600 transition duration-150 ease-in-out">
                                <flux:icon.envelope class="w-5 h-5 mr-1 text-blue-500" />
                                {{ $question->formEmail ?? '-' }}
                            </a>
                        @else
                            <span class="text-sm text-gray-700 dark:text-gray-200">{{ $question->formEmail ?? '-' }}</span>
                        @endif
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-3/5">
                        <span class="text-sm text-gray-700 dark:text-gray-200" style="word-break: break-word;">
                            {{ $question->message }}
                        </span>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="w-2/5 text-right">
                        <div class="flex items-center space-x-2">
                            @if (!$question->is_read)
                                <x-managers.ui.button wire:click="confirmMarkAsRead({{ $question->id }})"
                                    wire:loading.attr="disabled" variant="primary" size="sm"
                                    title="Tandai Sudah Dibaca">
                                    <x-icon.check class="w-4 h-4" />
                                </x-managers.ui.button>
                            @endif
                            <x-managers.ui.button wire:click="confirmDeleteQuestion({{ $question->id }})"
                                wire:loading.attr="disabled" variant="danger" size="sm" title="Hapus">
                                <x-icon.trash class="w-4 h-4" />
                            </x-managers.ui.button>
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