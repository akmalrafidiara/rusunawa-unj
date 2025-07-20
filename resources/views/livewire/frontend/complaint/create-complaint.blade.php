<?php

namespace App\Livewire\Frontend\Complaint;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\Report;
use App\Models\Contract; // Pastikan ini di-import
use App\Models\Occupant; // Pastikan ini di-import
use App\Enums\ReporterType;
use App\Enums\ReportStatus;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\LivewireFilepond\WithFilePond;
use Illuminate\Validation\Rule;
use App\Notifications\ReportNotification;
use App\Models\User;
use App\Enums\RoleUser;

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Buat Pengaduan')] class extends Component {
    use WithFileUploads;
    use WithFilePond;

    public $isLoggedIn;
    public ?Occupant $occupant = null; // Ini akan menyimpan objek Occupant yang login (jika PIC) atau terkait
    public ?Contract $contract = null; // Ini akan menyimpan objek Contract yang sedang login
    public $reporterType = '';
    public $reporterId = '';
    public $subject = '';
    public $description = '';
    public $attachments = [];
    public $filepondResetKey = 0;

    public $reporterOptions = [];
    public $occupantOptions = [];

    protected $messages = [
        'reporterType.required' => 'Jenis pelapor wajib dipilih.',
        'reporterId.required' => 'Penghuni yang melapor wajib dipilih.',
        'reporterId.exists' => 'Penghuni yang dipilih tidak valid.',
        'subject.required' => 'Subjek/Judul keluhan wajib diisi.',
        'subject.max' => 'Subjek/Judul keluhan tidak boleh lebih dari :max karakter.',
        'description.required' => 'Deskripsi keluhan wajib diisi.',
        'description.max' => 'Deskripsi keluhan tidak boleh lebih dari :max karakter.',
        'attachments.*.file' => 'Lampiran harus berupa file yang valid.',
        'attachments.*.mimes' => 'Format file yang diizinkan adalah gambar (jpeg, png, jpg, gif, webp) atau PDF.',
        'attachments.*.max' => 'Ukuran file lampiran tidak boleh lebih dari 2MB.',
    ];

    public function mount(): void
    {
        // PERBAIKAN UTAMA 1: Menggunakan guard 'contract' untuk memeriksa login
        $this->isLoggedIn = Auth::guard('contract')->check();

        // PERBAIKAN UTAMA 2: Mengambil objek Contract yang sedang login
        $this->contract = Auth::guard('contract')->user();

        if ($this->contract) {
            // Jika kontrak ditemukan, ambil objek Occupant PIC dari kontrak tersebut
            // Model Contract sudah punya relasi pic() yang menunjuk ke Occupant PIC
            $this->occupant = $this->contract->pic; // Ambil objek Occupant yang menjadi PIC

            if ($this->occupant) {
                // Pastikan PIC ada
                $this->reporterOptions = ReporterType::options();

                // Populasikan occupantOptions dari semua penghuni yang terkait dengan kontrak ini
                $this->occupantOptions = $this->contract->occupants
                    ->map(function ($occupant) {
                        return ['value' => $occupant->id, 'label' => $occupant->full_name];
                    })
                    ->toArray();

                // Set default reporterType dan panggil updatedReporterType untuk mengisi reporterId
                $this->reporterType = ReporterType::ROOM->value; // Default PIC sebagai pelapor
                $this->updatedReporterType();
            } else {
                // Handle case where contract is logged in but has no PIC
                $this->isLoggedIn = false;
                $this->contract = null;
                $this->occupant = null;
                LivewireAlert::warning('Login bermasalah')->text('Kontrak Anda tidak memiliki penanggung jawab yang valid.')->toast()->position('top-end')->show();
            }
        }
    }

    public function rules(): array
    {
        return [
            'reporterType' => ['required', Rule::in(ReporterType::values())],
            'reporterId' => [
                'required',
                'exists:occupants,id',
                function ($attribute, $value, $fail) {
                    // Pastikan reporterId yang dipilih memang terkait dengan kontrak yang login
                    if ($this->contract && !$this->contract->occupants->contains($value)) {
                        $fail('Penghuni yang dipilih tidak terdaftar pada kontrak ini.');
                    }
                },
            ],
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf|max:2048',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function updatedAttachments(): void
    {
        $this->resetErrorBag('attachments.*');
        $this->validateOnly('attachments.*');
    }

    public function updatedReporterType(): void
    {
        $this->resetErrorBag('reporterId');
        $this->resetValidation('reporterId');

        if ($this->reporterType === ReporterType::ROOM->value) {
            // PERBAIKAN UTAMA 3: Akses ID PIC langsung dari relasi belongsTo pic() pada Contract
            $this->reporterId = $this->contract->pic->id ?? null;
        } elseif ($this->reporterType === ReporterType::INDIVIDUAL->value) {
            // Jika individu, setel ke ID penghuni yang sebenarnya login (PIC)
            $this->reporterId = $this->occupant->id ?? null;
        } else {
            $this->reporterId = null;
        }
    }

    public function createReport(): void
    {
        $this->validate();

        // Validasi tambahan untuk memastikan penghuni terkait dengan kontrak
        // Ini sebenarnya sudah tercakup dalam rule 'reporterId', tapi sebagai validasi terakhir
        $selectedOccupant = Occupant::find($this->reporterId);
        if (!$selectedOccupant || !$selectedOccupant->contracts->contains($this->contract->id)) {
            $this->addError('reporterId', 'Penghuni yang dipilih tidak terdaftar pada kontrak ini.');
            return;
        }

        try {
            $report = Report::create([
                'contract_id' => $this->contract->id,
                'reporter_type' => $this->reporterType,
                'reporter_id' => $this->reporterId,
                'subject' => $this->subject,
                'description' => $this->description,
                'status' => ReportStatus::REPORT_RECEIVED,
            ]);

            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('reports/attachments', 'public');
                    $report->attachments()->create([
                        'name' => $file->getClientOriginalName(),
                        'file_name' => basename($path),
                        'mime_type' => $file->getMimeType(),
                        'path' => $path,
                    ]);
                }
            }

            $report->logs()->create([
                'user_id' => null, // Sistem yang membuat log awal ini
                'action_by_role' => 'Sistem',
                'old_status' => null,
                'new_status' => ReportStatus::REPORT_RECEIVED->value,
                'notes' => 'Laporan pengaduan baru telah dibuat oleh penghuni.',
            ]);

            $this->notifyNewReport($report);

            $this->redirect(route('complaint.success', ['unique_id' => $report->unique_id]), navigate: true);
        } catch (\Exception $e) {
            LivewireAlert::error('Gagal mengajukan laporan.')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Notifies ONLY the Head of Rusunawa and relevant Staff about a new report.
     * Admins are NOT notified at this stage.
     */
    public function notifyNewReport(Report $report)
    {
        $recipients = collect();
        $message = "Laporan keluhan baru #{$report->unique_id} telah diterima dan membutuhkan perhatian.";

        // 1. Get Head of Rusunawa
        $heads = User::role(RoleUser::HEAD_OF_RUSUNAWA->value)->get();
        $recipients = $recipients->merge($heads);

        // 2. Get Staff of the relevant building (jika ada unit cluster yang terkait)
        if ($report->contract->unit && $report->contract->unit->unitCluster) {
            $staffUsers = $report->contract->unit->unitCluster->staffUsers()->get();
            $recipients = $recipients->merge($staffUsers);
        }

        // Kirim notifikasi ke penerima unik
        foreach ($recipients->unique('id') as $user) {
            $user->notify(new ReportNotification($report, $message));
        }
    }
}; ?>

<section class="w-full">
    @include('modules.frontend.complaint.complaint-heading')
    <div class="container mx-auto relative overflow-hidden -mt-32 md:-mt-25 lg:-mt-25">
        <x-frontend.complaint.layout>
            <h1 class="text-3xl font-bold mb-8 hidden md:block dark:text-gray-100">Buat Pengaduan</h1>

            @if ($isLoggedIn && $occupant && $contract)
                <form wire:submit.prevent="createReport" class="space-y-6">
                    <p class="text-gray-700 dark:text-gray-300 text-sm italic">
                        Keluhan Anda akan terkait dengan ID Pemesanan: <span
                            class="font-bold">{{ $contract->contract_code }}</span>.
                    </p>

                    <div>
                        <x-frontend.complaint.form-label>Lapor Sebagai <span
                                class="text-red-500">*</span></x-frontend.complaint.form-label>
                        <x-frontend.complaint.form-select wire:model.live="reporterType" :options="ReporterType::options()"
                            label="Pilih Jenis Pelapor" />
                    </div>

                    @if ($reporterType === ReporterType::INDIVIDUAL->value)
                        <div>
                            <x-frontend.complaint.form-label>Pilih Penghuni yang Melapor <span
                                    class="text-red-500">*</span></x-frontend.complaint.form-label>
                            <x-frontend.complaint.form-select wire:model.live="reporterId" :options="$occupantOptions"
                                label="Pilih Penghuni" />
                        </div>
                    @else
                        <input type="hidden" wire:model="reporterId">
                    @endif

                    <div>
                        <x-frontend.complaint.form-label>Subjek Keluhan <span
                                class="text-red-500">*</span></x-frontend.complaint.form-label>
                        <x-frontend.complaint.form-input wire:model.live="subject"
                            placeholder="Contoh: AC kamar tidak dingin" />
                    </div>

                    <div>
                        <x-frontend.complaint.form-label>Deskripsi Detail Keluhan <span
                                class="text-red-500">*</span></x-frontend.complaint.form-label>
                        <x-frontend.complaint.form-textarea wire:model.live="description" rows="5"
                            placeholder="Jelaskan keluhan Anda secara detail..." />
                    </div>

                    <div>
                        <x-frontend.complaint.form-label>Lampiran (Foto/Dokumen)</x-frontend.complaint.form-label>
                        {{-- Tambahkan wire:key untuk mereset FilePond --}}
                        <div wire:key="filepond-attachments-wrapper-{{ $filepondResetKey }}">
                            <x-filepond::upload wire:model.live="attachments" multiple max-file-size="2MB"
                                accepted-file-types="image/*,application/pdf" />
                        </div>
                        <div class="mt-2">
                            @error('attachments.*')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @else
                                <x-frontend.complaint.form-small>Ukuran Maksimal: 2MB per file. Format: Gambar (JPG, PNG,
                                    dll.) atau PDF. Anda bisa mengunggah lebih dari satu.</x-frontend.complaint.form-small>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-frontend.complaint.button type="submit" variant="primary">
                            Kirim Pengaduan
                        </x-frontend.complaint.button>
                    </div>
                </form>
            @else
                <div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
                    <img src="{{ asset('images/dummy-pengaduan-kosong.png') }}" alt="Pengaduan Tidak Tersedia"
                        class="w-60 h-60 object-contain mx-auto">
                    <h2 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-2 dark:text-gray-100">Pengaduan Tidak
                        Tersedia</h2>
                    <p class="text-m lg:text-lg text-gray-600 mb-6 dark:text-gray-300">Mohon Login terlebih dahulu untuk
                        mengisi pengaduan</p>
                    <a href="{{ route('contract.auth', ['redirect' => url()->current()]) }}" wire:navigate
                        class="px-6 py-2 text-lg font-bold text-white transition duration-300 ease-in-out bg-green-500 rounded-full shadow-md hover:bg-green-600">
                        Login
                    </a>
                </div>
            @endif
        </x-frontend.complaint.layout>
    </div>
</section>
