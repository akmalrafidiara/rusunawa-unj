<?php

namespace App\Livewire\Managers\ReportsAndComplaints;

use App\Enums\ReportStatus;
use App\Enums\RoleUser;
use App\Models\Attachment;
use App\Models\Report;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Models\UnitCluster; // Import UnitCluster model

class ReportDetails extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $reportIdBeingViewed = null;
    public $reportUniqueId;
    public $reportSubject;
    public $reportDescription;
    public $reportReporterName;
    public $reportReporterType;
    public $reportReporterPhone;
    public $reportReporterEmail;
    public $reportRoomNumber;
    public $reportUnitClusterName;
    public $reportCreatedAt;
    public $reportUpdatedAt;
    public $reportCurrentStatus; // Enum instance
    public $reportCurrentHandlerName;
    public $reportLogs;
    public $reportInitialAttachments = [];
    public $completionDeadlineDaysLeft = null;
    public $isConfirmed = false;
    public $canConfirm = false;

    // Form properties for status update/notes/attachments
    public $newStatus = '';
    public $notes = '';
    public $newAttachments = [];

    // Modal visibility control
    public $showUpdateStatusModal = false;

    // Options
    public $availableStatusOptions = []; // Filtered options for status update form

    // User roles
    public $is_admin_user;
    public $is_head_of_rusunawa_user;
    public $is_staff_of_rusunawa_user;
    public $user_cluster_ids = [];

    protected $messages = [
        'newStatus.required' => 'ID Laporan wajib diisi.',
        'newStatus.in' => 'Status baru tidak valid.',
        'notes.required' => 'Catatan wajib diisi untuk perubahan status.',
        'notes.max' => 'Catatan tidak boleh lebih dari :max karakter.',
        'newAttachments.*.file' => 'Lampiran harus berupa file yang valid.',
        'newAttachments.*.mimes' => 'Format file yang diizinkan adalah gambar (jpeg, png, jpg, gif, webp) atau PDF.',
        'newAttachments.*.max' => 'Ukuran file lampiran tidak boleh lebih dari 2MB.',
    ];

    protected function rules(): array
    {
        return [
            'newStatus' => ['required', Rule::in(ReportStatus::values())],
            'notes' => 'required|string|max:1000',
            'newAttachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf|max:2048',
        ];
    }

    public function mount()
    {
        $this->is_admin_user = Auth::user()->hasRole(RoleUser::ADMIN->value);
        $this->is_head_of_rusunawa_user = Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value);
        $this->is_staff_of_rusunawa_user = Auth::user()->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value);

        // MODIFIED: Populate user_cluster_ids from the new many-to-many relationship
        if ($this->is_staff_of_rusunawa_user || $this->is_head_of_rusunawa_user) {
            $this->user_cluster_ids = Auth::user()->unitClusters->pluck('id')->toArray();
        }
    }

    #[On('reportSelected')]
    public function loadReportDetails($reportId)
    {
        $this->reset(['newStatus', 'notes', 'newAttachments']);
        $this->resetErrorBag();
        $this->resetValidation();

        $this->reportIdBeingViewed = $reportId;
        $report = Report::with([
            'reporter',
            'contract.unit.unitCluster',
            'currentHandler',
            'logs.user',
            'logs.attachments',
            'attachments'
        ])->find($reportId);

        if ($report) {
            $this->reportUniqueId = $report->unique_id;
            $this->reportSubject = $report->subject;
            $this->reportDescription = $report->description;
            $this->reportReporterName = $report->reporter->full_name ?? 'N/A';
            $this->reportReporterPhone = $report->reporter->whatsapp_number ?? 'N/A';
            $this->reportReporterEmail = $report->reporter->email ?? 'N/A';
            $this->reportReporterType = $report->reporter_type->label();
            $this->reportRoomNumber = $report->contract->unit->room_number ?? 'N/A';
            $this->reportUnitClusterName = $report->contract->unit->unitCluster->name ?? 'N/A';
            $this->reportCreatedAt = $report->created_at;
            $this->reportUpdatedAt = $report->updated_at;
            $this->reportCurrentStatus = $report->status;
            $this->reportCurrentHandlerName = $report->currentHandler->name ?? 'Belum Ditugaskan';
            $this->reportLogs = $report->logs()->orderBy('created_at', 'asc')->get();
            $this->reportInitialAttachments = $report->attachments->toArray();

            $this->isConfirmed = ($this->reportCurrentStatus === ReportStatus::CONFIRMED_COMPLETED);
            $this->canConfirm = ($this->reportCurrentStatus === ReportStatus::COMPLETED);

            if ($this->canConfirm && $report->completion_deadline) {
                $now = Carbon::now();
                $deadline = Carbon::parse($report->completion_deadline);
                $this->completionDeadlineDaysLeft = $now->diffInDays($deadline, false);

                if ($now->isAfter($deadline) && !$this->isConfirmed) {
                    $report->status = ReportStatus::CONFIRMED_COMPLETED;
                    $report->save();
                    $this->isConfirmed = true;
                    $this->canConfirm = false;
                    $this->completionDeadlineDaysLeft = 0;
                    $report->logs()->create([
                        'user_id' => null, 'action_by_role' => 'Sistem', 'old_status' => ReportStatus::COMPLETED->value, 'new_status' => ReportStatus::CONFIRMED_COMPLETED->value, 'notes' => 'Laporan otomatis dikonfirmasi selesai karena melewati batas waktu konfirmasi.',
                    ]);
                    LivewireAlert::info('Laporan otomatis dikonfirmasi selesai.')->text('Karena melewati batas waktu 7 hari konfirmasi.')->toast()->position('top-end')->show();
                }
            } else {
                $this->completionDeadlineDaysLeft = null;
            }

            $this->newStatus = $report->status->value;
            $this->generateAvailableStatusOptions();

        } else {
            $this->reportIdBeingViewed = null;
        }
    }

    public function render()
    {
        return view('livewire.managers.oprations.reports-and-complaints.report-details.index');
    }

    public function updateStatusForm()
    {
        if ($this->shouldDisableUpdateButton()) {
            return;
        }

        $this->generateAvailableStatusOptions();
        $this->showUpdateStatusModal = true;
    }

    public function saveStatusUpdate()
    {
        $report = Report::find($this->reportIdBeingViewed);
        if (!$report) {
            LivewireAlert::error('Laporan tidak ditemukan.')->toast()->position('top-end');
            $this->closeModal();
            return;
        }

        $oldStatus = $report->status->value;
        $currentUser = Auth::user();
        $userRole = $currentUser->getRoleNames()->first();

        $this->validate();

        if ($this->newStatus === ReportStatus::DISPOSED_TO_ADMIN->value && $userRole !== RoleUser::HEAD_OF_RUSUNAWA->value) {
            LivewireAlert::error('Akses Ditolak!')
                ->text('Hanya Kepala Rusunawa yang dapat mendisposisikan laporan ke Admin.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }
        if ($this->newStatus === ReportStatus::DISPOSED_TO_RUSUNAWA->value && $userRole !== RoleUser::ADMIN->value) {
            LivewireAlert::error('Akses Ditolak!')
                ->text('Hanya Admin yang dapat mengembalikan laporan ke Rusunawa.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $report->status = $this->newStatus;
        $report->current_handler_id = $currentUser->id;

        if ($this->newStatus === ReportStatus::COMPLETED->value) {
            $report->completion_deadline = Carbon::now()->addDays(7);
        } else if ($report->status !== ReportStatus::COMPLETED) {
            $report->completion_deadline = null;
        }

        $report->save();

        $logNotes = $this->notes;
        if ($this->newStatus === ReportStatus::DISPOSED_TO_RUSUNAWA->value && $oldStatus === ReportStatus::DISPOSED_TO_ADMIN->value) {
            $logNotes .= ' (Dikembalikan dari Admin)';
        }

        $newLog = $report->logs()->create([
            'user_id' => $currentUser->id,
            'action_by_role' => $userRole,
            'old_status' => $oldStatus,
            'new_status' => $this->newStatus,
            'notes' => $logNotes,
        ]);

        foreach ($this->newAttachments as $file) {
            $path = $file->store('reports/updates_attachments', 'public');
            $newLog->attachments()->create([
                'name' => $file->getClientOriginalName(),
                'file_name' => basename($path),
                'mime_type' => $file->getMimeType(),
                'path' => $path,
            ]);
        }

        LivewireAlert::title('Status laporan berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->closeModal();
        $this->dispatch('refreshReports')->to(ReportList::class);
        $this->loadReportDetails($report->id);
    }

    public function updatedNewAttachments()
    {
        $this->resetErrorBag('newAttachments.*');
        $this->validateOnly('newAttachments.*');
    }

    public function generateAvailableStatusOptions()
    {
        if (!$this->reportIdBeingViewed || !$this->reportCurrentStatus) {
            $this->availableStatusOptions = [];
            return;
        }

        $report = Report::find($this->reportIdBeingViewed);
        if (!$report) {
            $this->availableStatusOptions = [];
            return;
        }

        $currentStatus = ReportStatus::tryFrom($this->reportCurrentStatus->value);
        $options = [];

        if ($this->is_admin_user) {
            switch ($currentStatus) {
                case ReportStatus::DISPOSED_TO_ADMIN:
                case ReportStatus::IN_PROCESS:
                    $options[] = ['value' => ReportStatus::IN_PROCESS->value, 'label' => ReportStatus::IN_PROCESS->label()];
                    $options[] = ['value' => ReportStatus::DISPOSED_TO_RUSUNAWA->value, 'label' => ReportStatus::DISPOSED_TO_RUSUNAWA->label()];
                    $options[] = ['value' => ReportStatus::COMPLETED->value, 'label' => ReportStatus::COMPLETED->label()];
                    break;
                case ReportStatus::REPORT_RECEIVED:
                case ReportStatus::DISPOSED_TO_RUSUNAWA:
                    $options[] = ['value' => ReportStatus::IN_PROCESS->value, 'label' => ReportStatus::IN_PROCESS->label()];
                    break;
                case ReportStatus::COMPLETED:
                case ReportStatus::CONFIRMED_COMPLETED:
                    break;
            }
        }
        elseif ($this->is_head_of_rusunawa_user || $this->is_staff_of_rusunawa_user) {
            if ($report->currentHandler && $report->currentHandler->hasRole(RoleUser::ADMIN->value) &&
                $currentStatus !== ReportStatus::DISPOSED_TO_RUSUNAWA &&
                $currentStatus !== ReportStatus::COMPLETED &&
                $currentStatus !== ReportStatus::CONFIRMED_COMPLETED) {
                $options = [];
            } else {
                switch ($currentStatus) {
                    case ReportStatus::REPORT_RECEIVED:
                    case ReportStatus::IN_PROCESS:
                    case ReportStatus::DISPOSED_TO_RUSUNAWA:
                        $options[] = ['value' => ReportStatus::IN_PROCESS->value, 'label' => ReportStatus::IN_PROCESS->label()];
                        if ($this->is_head_of_rusunawa_user) {
                            $options[] = ['value' => ReportStatus::DISPOSED_TO_ADMIN->value, 'label' => ReportStatus::DISPOSED_TO_ADMIN->label()];
                        }
                        $options[] = ['value' => ReportStatus::COMPLETED->value, 'label' => ReportStatus::COMPLETED->label()];
                        break;
                    case ReportStatus::COMPLETED:
                    case ReportStatus::CONFIRMED_COMPLETED:
                    case ReportStatus::DISPOSED_TO_ADMIN:
                        break;
                }
            }
        } else {
            $options = [];
        }

        if ($currentStatus && !empty($options) && !in_array($currentStatus->value, array_column($options, 'value'))) {
            array_unshift($options, ['value' => $currentStatus->value, 'label' => $currentStatus->label()]);
        }
        
        if (empty($options) && $currentStatus &&
            $currentStatus !== ReportStatus::COMPLETED &&
            $currentStatus !== ReportStatus::CONFIRMED_COMPLETED) {
            $options[] = ['value' => $currentStatus->value, 'label' => $currentStatus->label(), 'disabled' => true];
        }


        $this->availableStatusOptions = collect($options)->unique('value')->values()->toArray();
    }

    /**
     * Mengatur VISIBILITAS tombol.
     * Tombol akan disembunyikan HANYA jika status laporan sudah final (Selesai).
     * @return bool
     */
    public function canEditReport(): bool
    {
        if (!$this->reportIdBeingViewed || !$this->reportCurrentStatus) {
            return false;
        }

        $report = Report::find($this->reportIdBeingViewed);
        if (!$report) {
            return false;
        }

        // Aturan Utama: Sembunyikan tombol jika status sudah 'COMPLETED' atau 'CONFIRMED_COMPLETED'.
        // Ini berlaku untuk semua role.
        if (in_array($report->status, [ReportStatus::COMPLETED, ReportStatus::CONFIRMED_COMPLETED])) {
            return false;
        }

        // Jika laporan belum selesai, tombol akan selalu ditampilkan.
        // Logika untuk menonaktifkan (disable) tombol akan ditangani oleh method `shouldDisableUpdateButton`.
        return true;
    }

    /**
     * Mengatur kondisi DISABLED pada tombol.
     * Tombol akan dinonaktifkan jika laporan sedang ditangani oleh tim lain.
     * @return bool
     */
    public function shouldDisableUpdateButton(): bool
    {
        if (!$this->reportIdBeingViewed) {
            return true;
        }

        $report = Report::find($this->reportIdBeingViewed);
        if (!$report) {
            return true;
        }

        $currentUser = Auth::user();
        $isCurrentUserAdmin = $currentUser->hasRole(RoleUser::ADMIN->value);
        
        // Cek siapa yang menangani laporan saat ini berdasarkan statusnya.
        // Ini lebih akurat daripada hanya melihat `currentHandler` terakhir.
        $isReportWithAdmin = false;
        if ($report->status === ReportStatus::DISPOSED_TO_ADMIN) {
            $isReportWithAdmin = true;
        } elseif ($report->status === ReportStatus::IN_PROCESS && $report->currentHandler && $report->currentHandler->hasRole(RoleUser::ADMIN->value)) {
            $isReportWithAdmin = true;
        }

        $isReportWithRusunawa = false;
        if (in_array($report->status, [
            ReportStatus::REPORT_RECEIVED,
            ReportStatus::DISPOSED_TO_RUSUNAWA,
        ])) {
            $isReportWithRusunawa = true;
        } elseif ($report->status === ReportStatus::IN_PROCESS && $report->currentHandler && !$report->currentHandler->hasRole(RoleUser::ADMIN->value)) {
            $isReportWithRusunawa = true;
        }

        // --- Logika Penonaktifan Tombol ---

        // Jika user saat ini adalah ADMIN
        if ($isCurrentUserAdmin) {
            // Tombol disable jika laporan sedang dipegang oleh tim Rusunawa.
            if ($isReportWithRusunawa) {
                return true;
            }
        } 
        // Jika user saat ini adalah KEPALA/STAFF RUSUNAWA
        else {
            // Tombol disable jika laporan sedang dipegang oleh ADMIN.
            if ($isReportWithAdmin) {
                return true;
            }
        }

        // Jika tidak ada kondisi di atas yang terpenuhi, tombol aktif.
        return false;
    }

    public function closeModal()
    {
        $this->showUpdateStatusModal = false;
        $this->reset(['newStatus', 'notes', 'newAttachments']);
        $this->resetErrorBag();
        $this->resetValidation();
    }
}