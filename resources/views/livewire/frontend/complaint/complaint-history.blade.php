<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Report;
use App\Models\Contract; // Pastikan model Contract di-import
use App\Enums\ReportStatus;
use App\Enums\ReporterType;

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Riwayat Pengaduan')] class extends Component {
    use WithPagination;

    public bool $isLoggedIn;
    // PERBAIKAN 1: Mengubah tipe properti dari Occupant menjadi Contract
    public ?Contract $contractUser = null;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    /**
     * Mount the component and initialize authentication state.
     */
    public function mount(): void
    {
        // PERBAIKAN 2: Menggunakan guard 'contract' untuk memeriksa login
        $this->isLoggedIn = Auth::guard('contract')->check();
        if ($this->isLoggedIn) {
            // PERBAIKAN 3: Menyimpan objek Contract yang login ke $this->contractUser
            $this->contractUser = Auth::guard('contract')->user();
        }
    }

    /**
     * Fetch the completed reports for the logged-in contract.
     */
    public function getReportsProperty()
    {
        // PERBAIKAN 4: Menggunakan $this->contractUser untuk memeriksa login dan mendapatkan ID kontrak
        if (!$this->isLoggedIn || !$this->contractUser) {
            return collect(); // Return an empty collection if not logged in
        }

        // PERBAIKAN 5: Mengambil laporan berdasarkan contract_id dari kontrak yang login.
        // Ini akan menampilkan semua laporan yang terkait dengan kontrak tersebut.
        return Report::where('contract_id', $this->contractUser->id)
            ->where('status', ReportStatus::CONFIRMED_COMPLETED)
            ->with(['reporter', 'contract.unit', 'logs'])
            ->orderByDesc('updated_at')
            ->paginate(5);
    }

    /**
     * Get the display color for a given report status.
     */
    public function getStatusColor(ReportStatus $status): string
    {
        return implode(' ', $status->color());
    }
}; ?>

<section class="w-full">
    {{-- Include the header section for the complaint pages --}}
    @include('modules.frontend.complaint.complaint-heading')

    <div class="container relative mx-auto -mt-32 overflow-hidden md:-mt-25 lg:-mt-25">
        <x-frontend.complaint.layout>
            <h1 class="hidden mb-8 text-3xl font-bold dark:text-gray-100 md:block">Riwayat Pengaduan</h1>

            {{-- PERBAIKAN 6: Menggunakan $contractUser untuk kondisi @if --}}
            @if ($isLoggedIn && $contractUser)
                @if ($this->reports->isNotEmpty())
                    <div class="space-y-6">
                        @foreach ($this->reports as $report)
                            <x-frontend.complaint.card class="p-6" wire:key="{{ $report->id }}">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $report->subject }}
                                    </h3>
                                    <span
                                        class="px-2 py-1 ml-2 text-xs rounded-full shrink-0 {{ $this->getStatusColor($report->status) }}">
                                        {{ $report->status->label() }}
                                    </span>
                                </div>

                                <p class="mb-1 text-sm text-gray-700 dark:text-gray-300">
                                    ID Laporan: <span class="font-semibold">{{ $report->unique_id }}</span>
                                </p>

                                {{-- Reporter Information --}}
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    Dilaporkan oleh
                                    @if ($report->reporter_type === ReporterType::ROOM)
                                        Kamar {{ $report->contract->unit->room_number ?? 'N/A' }}
                                    @else
                                        {{-- Relasi reporter() pada Report model masih menunjuk ke Occupant --}}
                                        {{ $report->reporter->full_name ?? 'N/A' }}
                                    @endif
                                    , {{ $report->created_at->translatedFormat('l, d F Y, H:i') }} WIB
                                </p>

                                {{-- Last Update Information --}}
                                @if ($report->logs->isNotEmpty())
                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                        Terakhir diupdate pada
                                        {{ $report->logs->first()->created_at->translatedFormat('l, d F Y, H:i') }} WIB
                                    </p>
                                @endif


                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ Str::limit($report->description, 100) }}</p>

                                <div class="mt-4">
                                    <a href="{{ route('complaint.history-detail', ['unique_id' => $report->unique_id]) }}"
                                        class="inline-flex items-center text-sm text-blue-600 hover:underline"
                                        wire:navigate>
                                        Lihat Detail →
                                    </a>
                                </div>
                            </x-frontend.complaint.card>
                        @endforeach

                        <x-frontend.pagination :paginator="$this->reports" />
                    </div>
                @else
                    {{-- Empty State: User is logged in but has no history --}}
                    <div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
                        <img src="{{ asset('images/dummy-pengaduan-kosong.png') }}" alt="Riwayat Pengaduan Kosong"
                            class="w-60 h-60 object-contain mx-auto">
                        <h2 class="text-xl font-semibold text-gray-800 lg:text-2xl dark:text-gray-100">Riwayat Pengaduan
                            Kosong</h2>
                        <p class="mb-6 text-gray-600 text-m lg:text-lg dark:text-gray-300">Anda belum memiliki pengaduan
                            yang telah selesai.</p>
                        <a href="{{ route('complaint.create-complaint') }}" wire:navigate
                            class="px-6 py-2 text-lg font-bold text-white transition duration-300 ease-in-out bg-emerald-500 rounded-full shadow-md hover:bg-emerald-600">
                            Buat Pengaduan Baru
                        </a>
                    </div>
                @endif
            @else
                {{-- Not Logged In State --}}
                <div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
                    <img src="{{ asset('images/dummy-pengaduan-kosong.png') }}" alt="Harap Login"
                        class="w-60 h-60 object-contain mx-auto">
                    <h2 class="text-xl font-semibold text-gray-800 lg:text-2xl dark:text-gray-100">Anda Belum Login</h2>
                    <p class="mb-6 text-gray-600 text-m lg:text-lg dark:text-gray-300">Silakan login terlebih dahulu
                        untuk melihat riwayat pengaduan Anda.</p>
                    <a href="{{ route('contract.auth', ['redirect' => url()->current()]) }}" wire:navigate
                        class="px-6 py-2 text-lg font-bold text-white transition duration-300 ease-in-out bg-green-500 rounded-full shadow-md hover:bg-green-600">
                        Login
                    </a>
                </div>
            @endif
        </x-frontend.complaint.layout>
    </div>
</section>
