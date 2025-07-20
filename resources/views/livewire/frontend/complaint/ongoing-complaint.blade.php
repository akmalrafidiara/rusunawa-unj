<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Report;
use App\Models\Contract; // Import Contract model
use App\Enums\ReportStatus;
use App\Enums\ReporterType;

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Pengaduan Berjalan')] class extends Component {
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
     * Returns the base query for ongoing reports.
     */
    private function getBaseOngoingReportsQuery()
    {
        // PERBAIKAN 4: Menggunakan $this->contractUser untuk memeriksa login dan mendapatkan ID kontrak
        if (!$this->isLoggedIn || !$this->contractUser) {
            // Return a query that will yield no results if not logged in.
            return Report::whereRaw('false');
        }

        $ongoingStatuses = [ReportStatus::REPORT_RECEIVED, ReportStatus::IN_PROCESS, ReportStatus::DISPOSED_TO_ADMIN, ReportStatus::DISPOSED_TO_RUSUNAWA, ReportStatus::COMPLETED];

        // PERBAIKAN 5: Mengambil laporan berdasarkan contract_id dari kontrak yang login.
        // Ini akan menampilkan semua laporan yang terkait dengan kontrak tersebut.
        return Report::where('contract_id', $this->contractUser->id)->whereIn('status', $ongoingStatuses);
    }

    /**
     * Fetch the paginated ongoing reports.
     */
    public function getReportsProperty()
    {
        return $this->getBaseOngoingReportsQuery()
            ->with(['reporter', 'contract.unit', 'logs'])
            ->orderByDesc('updated_at')
            ->paginate(5);
    }

    /**
     * Get the count of active (ongoing) complaints.
     */
    public function getActiveComplaintCountProperty(): int
    {
        return $this->getBaseOngoingReportsQuery()->count();
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
            <h1 class="hidden mb-8 text-3xl font-bold dark:text-gray-100 md:block">
                Pengaduan Berjalan ({{ $this->activeComplaintCount }})
            </h1>

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
                                    , pada {{ $report->created_at->translatedFormat('l, d F Y, H:i') }} WIB
                                </p>

                                {{-- Last Update Information --}}
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                    Terakhir diupdate pada {{ $report->updated_at->translatedFormat('l, d F Y, H:i') }}
                                    WIB
                                </p>

                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ Str::limit($report->description, 100) }}</p>

                                <div class="mt-4">
                                    <a href="{{ route('complaint.ongoing-detail', ['unique_id' => $report->unique_id]) }}"
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
                    {{-- Empty State: User is logged in but has no ongoing complaints --}}
                    <div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
                        <img src="{{ asset('images/dummy-pengaduan-kosong.png') }}" alt="Tidak Ada Pengaduan"
                            class="w-60 h-60 object-contain mx-auto">
                        <h2 class="text-xl font-semibold text-gray-800 lg:text-2xl dark:text-gray-100">Tidak Ada
                            Pengaduan Berjalan</h2>
                        <p class="mb-6 text-gray-600 text-m lg:text-lg dark:text-gray-300">Saat ini Anda tidak memiliki
                            pengaduan yang sedang diproses.</p>
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
                        untuk melihat pengaduan Anda.</p>
                    <a href="{{ route('contract.auth', ['redirect' => url()->current()]) }}" wire:navigate
                        class="px-6 py-2 text-lg font-bold text-white transition duration-300 ease-in-out bg-green-500 rounded-full shadow-md hover:bg-green-600">
                        Login
                    </a>
                </div>
            @endif
        </x-frontend.complaint.layout>
    </div>
</section>
