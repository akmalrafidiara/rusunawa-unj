<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Report;
use App\Enums\ReportStatus;
use App\Enums\RoleUser;
use App\Enums\ReporterType;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Detail Pengaduan')] class extends Component
{
    public ?Report $report = null;
    public $confirmationDeadlineDaysLeft = null;
    public $isConfirmed = false;
    public $canConfirm = false;

    public function mount(string $unique_id): void
    {
        // Eager load contract.unit for room_number and reporter for name
        // Eager load logs and their attachments for display
        $this->report = Report::with(['reporter', 'contract.unit', 'currentHandler', 'logs.user', 'logs.attachments', 'attachments'])
            ->where('unique_id', $unique_id)
            ->firstOrFail();

        $this->isConfirmed = ($this->report->status === ReportStatus::CONFIRMED_COMPLETED);
        $this->canConfirm = ($this->report->status === ReportStatus::COMPLETED);

        if ($this->canConfirm && $this->report->completion_deadline) {
            $now = Carbon::now();
            $deadline = Carbon::parse($this->report->completion_deadline);
            $this->confirmationDeadlineDaysLeft = $now->diffInDays($deadline, false);

            if ($now->isAfter($deadline) && !$this->isConfirmed) {
                $this->report->status = ReportStatus::CONFIRMED_COMPLETED;
                $this->report->save();
                $this->isConfirmed = true;
                $this->canConfirm = false;
                $this->confirmationDeadlineDaysLeft = 0;

                $this->report->logs()->create([
                    'user_id' => null,
                    'action_by_role' => 'Sistem',
                    'old_status' => ReportStatus::COMPLETED->value,
                    'new_status' => ReportStatus::CONFIRMED_COMPLETED->value,
                    'notes' => 'Laporan otomatis dikonfirmasi selesai karena melewati batas waktu konfirmasi.',
                ]);
                LivewireAlert::info('Laporan otomatis dikonfirmasi selesai.')
                    ->text('Karena melewati batas waktu 7 hari konfirmasi.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
        }
    }

    public function confirmCompletion(): void
    {
        if ($this->report && $this->report->status === ReportStatus::COMPLETED) {
            $this->report->status = ReportStatus::CONFIRMED_COMPLETED;
            $this->report->completion_deadline = null;
            $this->report->save();

            $this->report->logs()->create([
                'user_id' => null,
                'action_by_role' => 'Penghuni',
                'old_status' => ReportStatus::COMPLETED->value,
                'new_status' => ReportStatus::CONFIRMED_COMPLETED->value,
                'notes' => 'Pengaduan dikonfirmasi selesai oleh penghuni.',
            ]);

            $this->isConfirmed = true;
            $this->canConfirm = false;
            $this->confirmationDeadlineDaysLeft = null;

            LivewireAlert::success('Konfirmasi berhasil!')
                ->text('Pengaduan Anda telah dikonfirmasi selesai.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function getStatusColor($status): string
    {
        $enum = ReportStatus::tryFrom($status);
        return $enum ? implode(' ', $enum->color()) : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
    }
}; ?>

<section class="container mx-auto px-4 py-2 md:py-6 relative overflow-hidden">
    <div class="relative w-full py-0 px-4 sm:px-6 lg:px-8 mb-6 mt-6 overflow-hidden">
        {{-- Tombol Kembali --}}
        <button onclick="history.back()"
            class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-white dark:hover:text-zinc-200 mb-6 font-medium">
            <flux:icon name="chevron-left" class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" />
            Kembali
        </button>

        <h1 class="text-3xl font-bold mb-8 hidden md:block dark:text-gray-100">Detail Pengaduan #{{ $report->unique_id }}</h1>

        {{-- Pembungkus untuk layout dua kolom: Informasi Laporan dan Status & Waktu --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Card 1: Informasi Laporan --}}
            <x-frontend.complaint.card class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.information-circle class="w-6 h-6 text-blue-500 dark:text-blue-400" />
                    Informasi Laporan
                </h3>
                <div class="space-y-2 text-gray-700 dark:text-gray-300">
                    <p><strong>Subjek:</strong> {{ $report->subject }}</p>
                    <p><strong>Dilaporkan oleh:</strong>
                        {{ $report->reporter_type->label() }} -
                        @if ($report->reporter_type === \App\Enums\ReporterType::ROOM)
                        Kamar {{ $report->contract->unit->room_number ?? 'N/A' }}
                        @elseif ($report->reporter_type === \App\Enums\ReporterType::INDIVIDUAL)
                        {{ $report->reporter->full_name ?? 'N/A' }}
                        @else
                        N/A
                        @endif
                    </p>
                    {{-- "Dibuat Pada" dipindahkan ke sini --}}
                    <p><strong>Dibuat Pada:</strong> {{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </x-frontend.complaint.card>

            {{-- Card 2: Status & Waktu --}}
            <x-frontend.complaint.card class="p-6">
                <div class="space-y-2 text-gray-700 dark:text-gray-300 mb-6">
                    <div class="flex justify-between items-center">
                        <strong>Status Saat Ini:</strong>
                        <span class="px-2 py-1 rounded-full text-xs {{ implode(' ', $report->status->color()) }}">{{ $report->status->label() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <strong>Terakhir Diperbarui:</strong>
                        <span>{{ $report->updated_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                    <hr class="border-gray-200 dark:border-zinc-600 my-4">
                    <div class="flex justify-between items-center">
                        <strong>Konfirmasi Selesai:</strong>
                        <x-frontend.complaint.button wire:click="confirmCompletion"
                            variant="primary"
                            class="{{ !$canConfirm ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}"
                            :disabled="!$canConfirm || $isConfirmed"
                            title="{{ !$canConfirm || $isConfirmed ? 'Laporan belum dapat dikonfirmasi oleh Anda atau sudah dikonfirmasi.' : '' }}">
                            {{ $isConfirmed ? 'Selesai' : 'Keluhan Selesai' }}
                        </x-frontend.complaint.button>
                    </div>
                </div>
            </x-frontend.complaint.card>
        </div>

        {{-- Bagian Konfirmasi Penyelesaian (jika belum dikonfirmasi) --}}
        <div class="mb-6">
            @if ($canConfirm || $isConfirmed)
            @if ($canConfirm)
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg text-center">
                <p class="text-yellow-800 dark:text-yellow-300 font-semibold mb-2">
                    Laporan ini dinyatakan selesai oleh Pengelola. Mohon konfirmasi jika sudah sesuai.
                </p>
                @if ($confirmationDeadlineDaysLeft !== null)
                <p class="text-yellow-700 dark:text-yellow-400 text-sm mb-3">
                    @if (abs($confirmationDeadlineDaysLeft) <= 1)
                        @php
                        $deadline=Carbon::parse($report->completion_deadline);
                        $now = Carbon::now();
                        $diffInHours = $now->diffInHours($deadline, false);
                        $diffInMinutes = $now->diffInMinutes($deadline, false) % 60;
                        @endphp
                        Batas waktu konfirmasi: <span class="font-bold">{{ abs($diffInHours) }} jam {{ abs($diffInMinutes) }} menit lagi</span>
                        @else
                        Batas waktu konfirmasi: <span class="font-bold">{{ round(abs($confirmationDeadlineDaysLeft)) }} hari lagi.</span>
                        @endif
                        (Batas waktu: {{ $report->completion_deadline->translatedFormat('d F Y') }})
                </p>
                @endif
            </div>
            @elseif($isConfirmed)
            <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg text-center text-green-800 dark:text-green-300 font-semibold">
                Laporan ini telah Anda konfirmasi selesai.
            </div>
            @endif
            @endif
        </div>

        {{-- Deskripsi Laporan --}}
        <div class="mb-6">
            <x-frontend.complaint.card class="p-6">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <flux:icon.document-text class="w-6 h-6 text-orange-500 dark:text-orange-400" />
                    Deskripsi Laporan
                </h4>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $report->description }}</p>

                {{-- Lampiran Gambar Laporan di dalam Deskripsi --}}
                @php
                $imageAttachments = $report->attachments->filter(fn($att) => Str::startsWith($att->mime_type, 'image/'));
                @endphp
                @if ($imageAttachments->isNotEmpty())
                <h5 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-2">Gambar Lampiran:</h5>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach ($imageAttachments as $attachment)
                    <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="block">
                        <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->name }}" class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700">
                    </a>
                    @endforeach
                </div>
                @endif
            </x-frontend.complaint.card>
        </div>

        {{-- Riwayat Status & Catatan dalam Card --}}
        <x-frontend.complaint.card class="p-6">
            <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <flux:icon.list-bullet class="w-6 h-6 text-green-500 dark:text-green-400" />
                Riwayat Laporan & Catatan
            </h4>
            <div class="space-y-4">
                @forelse ($report->logs->sortByDesc('created_at') as $log)
                <div class="p-3 rounded-lg border border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800">
                    {{-- Changed from justify-between to flex-col sm:flex-row and added gap-2 --}}
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-1 gap-2">
                        <span class="font-bold text-gray-900 dark:text-white text-base sm:text-lg">
                            Status: <span class="px-2 py-0.5 rounded-full text-xs {{ implode(' ', $log->new_status->color()) }}">{{ $log->new_status->label() }}</span>
                        </span>
                        <span class="text-xs text-gray-600 dark:text-gray-400 sm:text-sm">
                            {{ $log->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                        Oleh: {{ $log->user->name ?? $log->action_by_role ?? 'Sistem' }}
                        @if($log->user)
                        @php
                        $userRole = $log->user->getRoleNames()->first();
                        $displayRole = $userRole ? \App\Enums\RoleUser::tryFrom($userRole)?->label() ?? $userRole : 'User';
                        @endphp
                        ({{ $displayRole }})
                        @endif
                    </p>
                    @if ($log->notes)
                    <p class="text-sm text-gray-800 dark:text-gray-200">Catatan: {{ $log->notes }}</p>
                    @endif
                    @if ($log->attachments->isNotEmpty())
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Lampiran Pengerjaan:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($log->attachments as $attachment)
                            <a href="{{ Illuminate\Support\Facades\Storage::url($attachment->path) }}" target="_blank" class="block">
                                @if (Illuminate\Support\Str::startsWith($attachment->mime_type, 'image'))
                                <img src="{{ Illuminate\Support\Facades\Storage::url($attachment->path) }}" class="w-full h-20 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700" alt="{{ $attachment->name }}">
                                @else
                                <div class="w-full h-20 flex flex-col items-center justify-center bg-gray-100 rounded-lg shadow-sm text-gray-500 dark:bg-zinc-700 dark:text-gray-400 border border-gray-200 dark:border-zinc-700">
                                    <flux:icon.document class="w-6 h-6 mb-1" />
                                    <p class="text-xs text-center px-1 truncate w-full">{{ $attachment->name }}</p>
                                </div>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-center text-gray-500 dark:text-gray-400">Belum ada riwayat status.</p>
                @endforelse
            </div>
        </x-frontend.complaint.card>
    </div>
</section>