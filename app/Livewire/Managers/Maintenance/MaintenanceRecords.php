<?php

namespace App\Livewire\Managers\Maintenance;

use App\Enums\MaintenanceRecordStatus;
use App\Enums\MaintenanceRecordType;
use App\Enums\MaintenanceScheduleStatus;
use App\Models\Attachment;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\Unit;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleUser;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LivewireFilepond\WithFilePond;
use Livewire\Attributes\On;


class MaintenanceRecords extends Component
{
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // --- Properties for Maintenance Record Form ---
    public $recordIdBeingEdited = null;
    public $recordUnitId = '';
    public $recordMaintenanceScheduleId = null;
    public $recordType = MaintenanceRecordType::ROUTINE->value;
    public $recordScheduledDate;
    public $recordCompletionDate;
    public $recordStatus = '';
    public $recordNotes = '';
    public $recordAttachments = [];
    public $existingRecordAttachments = [];
    public $attachmentsToDelete = [];
    public $isLate = false;

    // --- General UI properties ---
    public $showRecordModal = false;
    public $showRecordDetailModal = false;
    public $modalType = ''; // 'create_record', 'edit_record', 'detail_record'

    // Properties for handling selected schedule from parent
    public $selectedSchedule = null;
    public $selectedScheduleIdFromParent = null; // New property to store the ID from parent

    // Private property to hold the paginator instance for related records
    private $relatedRecordsPaginator = null;

    // Options for dropdowns
    public $allAcUnitOptions = []; // Digunakan untuk dropdown unit di form record
    public $recordTypeOptions = [];
    public $recordStatusOptions = [];

    // User roles for conditional rendering
    public $is_admin_user;
    public $is_head_of_rusunawa_user;

    // Pagination for records (independent of schedule pagination)
    public $perPage = 5; // Default for records history
    public $recordHistoryPage = 1;

    protected $queryString = [
        'recordHistoryPage' => ['except' => 1, 'as' => 'riwayatPage'], // Query string untuk paginasi riwayat
    ];

    protected $listeners = [
        'scheduleSelected' => 'handleScheduleSelected', // Listen to the event from MaintenanceSchedules
        // Pastikan listener untuk event dispatch dari komponen MaintenanceSchedules ada jika Anda menggunakannya
        'editSchedule' => 'editScheduleOnParent', // Listener untuk event editSchedule
        'confirmDeleteSchedule' => 'confirmDeleteScheduleOnParent', // Listener untuk event confirmDeleteSchedule
    ];

    protected $messages = [
        'recordUnitId.required' => 'Unit wajib dipilih.',
        'recordUnitId.exists' => 'Unit yang dipilih tidak valid.',
        'recordType.required' => 'Tipe pemeliharaan wajib dipilih.',
        'recordType.in' => 'Tipe pemeliharaan tidak valid.',
        'recordScheduledDate.required' => 'Tanggal terjadwal wajib diisi.',
        'recordScheduledDate.date' => 'Tanggal terjadwal harus berupa tanggal yang valid.',
        'recordCompletionDate.required' => 'Tanggal penyelesaian wajib diisi.',
        'recordCompletionDate.date' => 'Tanggal penyelesaian harus berupa tanggal yang valid.',
        'recordCompletionDate.after_or_equal' => 'Tanggal penyelesaian tidak boleh lebih awal dari tanggal terjadwal.',
        'recordNotes.string' => 'Catatan harus berupa teks.',
        'recordNotes.max' => 'Catatan tidak boleh lebih dari :max karakter.',
        'recordAttachments.*.file' => 'File harus berupa file yang valid.',
        'recordAttachments.*.mimes' => 'Format file yang diizinkan adalah JPG, JPEG, PNG, GIF, atau PDF.',
        'recordAttachments.*.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
    ];

    protected function rules(): array
    {
        return [
            'recordUnitId' => 'required|exists:units,id',
            'recordMaintenanceScheduleId' => 'nullable|exists:maintenance_schedules,id',
            'recordType' => ['required', Rule::in(MaintenanceRecordType::values())],
            'recordScheduledDate' => 'required|date',
            'recordCompletionDate' => 'required|date',
            'recordNotes' => 'nullable|string|max:1000',
            'recordAttachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ];
    }

    public function mount()
    {
        $this->allAcUnitOptions = Unit::select('id', 'room_number', 'unit_cluster_id')
            ->with(['unitCluster', 'unitType'])
            ->whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))
            ->orderBy('room_number')
            ->get()
            ->map(fn($unit) => [
                'value' => $unit->id,
                'label' => 'Kamar ' . $unit->room_number . (optional($unit->unitCluster)->name ? ' - ' . $unit->unitCluster->name : '')
            ])
            ->toArray();

        $this->recordTypeOptions = MaintenanceRecordType::options();
        $this->recordStatusOptions = MaintenanceRecordStatus::options();

        $this->is_admin_user = Auth::user()->hasRole(RoleUser::ADMIN->value);
        $this->is_head_of_rusunawa_user = Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value);
    }

    #[On('scheduleSelected')]
    public function handleScheduleSelected($scheduleId)
    {
        $this->selectedScheduleIdFromParent = $scheduleId;
        if ($scheduleId) {
            $this->selectedSchedule = MaintenanceSchedule::with(['unit.unitCluster'])->find($scheduleId);
        } else {
            $this->selectedSchedule = null;
        }
        $this->resetPage('recordHistoryPage'); // Reset record history pagination when schedule changes
    }

    public function render()
    {
        // Only query records if a schedule is selected
        if ($this->selectedScheduleIdFromParent) {
            $this->selectedSchedule = MaintenanceSchedule::with(['unit.unitCluster'])->find($this->selectedScheduleIdFromParent);
            if ($this->selectedSchedule) {
                $this->relatedRecordsPaginator = MaintenanceRecord::where(function ($query) {
                    $query->where('maintenance_schedule_id', $this->selectedSchedule->id);
                    $query->orWhere(function ($q) {
                        $q->where('unit_id', $this->selectedSchedule->unit_id)
                            ->where('type', \App\Enums\MaintenanceRecordType::URGENT->value);
                    });
                })
                    ->orderBy('completion_date', 'desc')
                    ->paginate($this->perPage, pageName: 'recordHistoryPage');
            } else {
                $this->relatedRecordsPaginator = new LengthAwarePaginator([], 0, $this->perPage, 1, ['pageName' => 'recordHistoryPage']);
            }
        } else {
            $this->relatedRecordsPaginator = new LengthAwarePaginator([], 0, $this->perPage, 1, ['pageName' => 'recordHistoryPage']);
        }

        return view('livewire.managers.oprations.maintenance.maintenance-records.index', [
            'relatedRecordsPaginator' => $this->relatedRecordsPaginator,
            'selectedSchedule' => $this->selectedSchedule, // Pass selected schedule to view
            'is_admin_user' => $this->is_admin_user,
            'is_head_of_rusunawa_user' => $this->is_head_of_rusunawa_user,
        ]);
    }

    // Record Methods
    public function createRecord($scheduleId = null, $unitIdForUrgent = null)
    {
        $this->resetRecordForm();
        $this->modalType = 'create_record';
        $this->showRecordModal = true;
        $this->recordMaintenanceScheduleId = $scheduleId;

        if ($scheduleId) {
            $schedule = MaintenanceSchedule::find($scheduleId);
            if (!$schedule) {
                LivewireAlert::error('Jadwal rutin tidak ditemukan.')->toast()->position('top-end');
                return;
            }
            $this->recordUnitId = $schedule->unit_id;
            $this->recordType = MaintenanceRecordType::ROUTINE->value;
            $this->recordScheduledDate = $schedule->next_due_date->format('Y-m-d');
            $this->recordStatus = MaintenanceRecordStatus::SCHEDULED->value;
        } else {
            $this->recordType = MaintenanceRecordType::URGENT->value;
            $this->recordScheduledDate = Carbon::now()->format('Y-m-d');
            $this->recordStatus = MaintenanceRecordStatus::URGENT->value;
            if ($unitIdForUrgent) {
                $this->recordUnitId = $unitIdForUrgent;
            } else {
                if (!empty($this->allAcUnitOptions)) {
                    $this->recordUnitId = $this->allAcUnitOptions[0]['value'];
                }
            }
        }
    }

    public function editRecord(MaintenanceRecord $record)
    {
        $this->resetValidation();
        $this->recordIdBeingEdited = $record->id;
        $this->recordUnitId = $record->unit_id;
        $this->recordMaintenanceScheduleId = $record->maintenance_schedule_id;
        $this->recordType = $record->type->value;
        $this->recordScheduledDate = $record->scheduled_date->format('Y-m-d');
        $this->recordCompletionDate = $record->completion_date?->format('Y-m-d');
        $this->recordStatus = $record->status->value;
        $this->recordNotes = $record->notes;
        $this->existingRecordAttachments = $record->attachments->toArray();
        $this->recordAttachments = [];

        $this->modalType = 'edit_record';
        $this->showRecordModal = true;
    }

    public function detailRecord(MaintenanceRecord $record)
    {
        $this->resetValidation(); // Clear validation for detail modal
        $this->recordIdBeingEdited = $record->id; // Set ID for detail too, if needed for fillData
        $this->recordUnitId = $record->unit_id;
        $this->recordMaintenanceScheduleId = $record->maintenance_schedule_id;
        $this->recordType = $record->type->value;
        $this->recordScheduledDate = $record->scheduled_date->format('Y-m-d');
        $this->recordCompletionDate = $record->completion_date?->format('d F Y');
        $this->recordStatus = $record->status->value;
        $this->recordNotes = $record->notes;
        $this->existingRecordAttachments = $record->attachments->toArray();
        $scheduled = Carbon::parse($this->recordScheduledDate);
        $completed = $record->completion_date ? Carbon::parse($record->completion_date) : null;
        $this->isLate = $completed ? $completed->greaterThan($scheduled) : false;

        $this->modalType = 'detail_record';
        $this->showRecordDetailModal = true;
    }

    public function saveRecord()
    {
        $this->validate($this->rules());

        $data = [
            'unit_id' => $this->recordUnitId,
            'maintenance_schedule_id' => $this->recordMaintenanceScheduleId,
            'type' => $this->recordType,
            'scheduled_date' => $this->recordScheduledDate,
            'notes' => $this->recordNotes,
        ];

        if ($this->recordType === MaintenanceRecordType::URGENT->value) {
            $data['status'] = MaintenanceRecordStatus::URGENT->value;
            $data['is_late'] = false;
            if (!empty($this->recordCompletionDate)) {
                $data['completion_date'] = Carbon::parse($this->recordCompletionDate);
            } else {
                $data['completion_date'] = null;
            }
        } else {
            $scheduledDate = Carbon::parse($this->recordScheduledDate);
            if (!empty($this->recordCompletionDate)) {
                $completionDate = Carbon::parse($this->recordCompletionDate);

                if ($completionDate->lt($scheduledDate)) {
                    $data['status'] = MaintenanceRecordStatus::COMPLETED_EARLY->value;
                    $data['is_late'] = false;
                } elseif ($completionDate->eq($scheduledDate)) {
                    $data['status'] = MaintenanceRecordStatus::COMPLETED_ON_TIME->value;
                    $data['is_late'] = false;
                } else {
                    $data['status'] = MaintenanceRecordStatus::COMPLETED_LATE->value;
                    $data['is_late'] = true;
                }
                $data['completion_date'] = $completionDate;
            } else {
                $data['completion_date'] = null;
                $data['is_late'] = false;
                $data['status'] = MaintenanceRecordStatus::SCHEDULED->value;
            }
        }

        $record = MaintenanceRecord::updateOrCreate(
            ['id' => $this->recordIdBeingEdited],
            $data
        );

        $this->handleAttachmentDeletions($record);
        $this->handleAttachmentUploads($record);

        if ($record->type->value === MaintenanceRecordType::ROUTINE->value && $record->maintenanceSchedule) {
            $schedule = $record->maintenanceSchedule;
            $oldScheduleStatus = $schedule->status->value;

            if ($record->completion_date) {
                $schedule->next_due_date = Carbon::parse($record->completion_date)->addMonths($schedule->frequency_months);
                $schedule->last_completed_at = $record->completion_date;
                $schedule->status = MaintenanceScheduleStatus::SCHEDULED;
            } else if ($record->status->value === MaintenanceRecordStatus::POSTPONED->value) {
                $schedule->status = MaintenanceScheduleStatus::POSTPONED;
            }

            if ($oldScheduleStatus !== MaintenanceScheduleStatus::SCHEDULED->value && $schedule->status->value === MaintenanceScheduleStatus::SCHEDULED->value) {
                $schedule->notes = null;
            }
            $schedule->save();
        }

        LivewireAlert::title($this->recordIdBeingEdited ? 'Rekaman pemeliharaan berhasil diperbarui.' : 'Rekaman pemeliharaan berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetRecordForm();
        $this->showRecordModal = false;
    }

    public function queueAttachmentForDeletion($attachmentId)
    {
        if (!in_array($attachmentId, $this->attachmentsToDelete)) {
            $this->attachmentsToDelete[] = $attachmentId;
        }

        $this->existingRecordAttachments = collect($this->existingRecordAttachments)->reject(function ($attachment) use ($attachmentId) {
            return $attachment['id'] == $attachmentId;
        })->values();

        LivewireAlert::success('File ditandai untuk dihapus.')->toast()->position('top-end');
    }

    public function removeNewAttachment($index)
    {
        if (isset($this->recordAttachments[$index])) {
            unset($this->recordAttachments[$index]);
            $this->recordAttachments = array_values($this->recordAttachments);
            LivewireAlert::success('File berhasil dihapus dari daftar unggah.')->toast()->position('top-end');
        }
    }

    private function handleAttachmentDeletions(MaintenanceRecord $record)
    {
        if (!empty($this->attachmentsToDelete)) {
            $attachments = Attachment::whereIn('id', $this->attachmentsToDelete)->get();
            foreach ($attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }
        }
    }

    private function handleAttachmentUploads(MaintenanceRecord $record)
    {
        if (!empty($this->recordAttachments)) {
            foreach ($this->recordAttachments as $file) {
                $path = '';
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $path = $file->store('maintenance_attachments/images', 'public');
                } else {
                    $path = $file->store('maintenance_attachments/files', 'public');
                }

                $record->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'file_name' => basename($path),
                    'mime_type' => $file->getMimeType(),
                    'path' => $path,
                ]);
            }
        }
    }

    public function confirmDeleteRecord($id)
    {
        LivewireAlert::title('Hapus Rekaman Pemeliharaan?')
            ->text('Apakah Anda yakin ingin menghapus rekaman pemeliharaan ini? Ini akan menghapus semua file yang terkait.')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteRecord', ['id' => $id])
            ->show();
    }

    public function deleteRecord($data)
    {
        try {
            $record = MaintenanceRecord::find($data['id']);
            if ($record) {
                foreach ($record->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment->path);
                    $attachment->delete();
                }
                $record->delete();
                LivewireAlert::success('Rekaman pemeliharaan berhasil dihapus.')->toast()->position('top-end');
            }
        } catch (Throwable | ValidationException $e) {
            Log::error('Error deleting maintenance record: ' . $e->getMessage(), ['exception' => $e]);
            LivewireAlert::error('Gagal menghapus rekaman pemeliharaan.')->toast()->position('top-end')->show();
        }
    }

    private function resetRecordForm()
    {
        $this->resetValidation();
        $this->reset([
            'recordIdBeingEdited',
            'recordUnitId',
            'recordMaintenanceScheduleId',
            'recordType',
            'recordScheduledDate',
            'recordCompletionDate',
            'recordStatus',
            'recordNotes',
            'recordAttachments',
            'existingRecordAttachments',
            'attachmentsToDelete',
        ]);
        $this->recordType = MaintenanceRecordType::ROUTINE->value;
        $this->recordStatus = MaintenanceRecordStatus::SCHEDULED->value;
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'record')) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    // --- Metode baru untuk menangani dispatch dari tombol Edit/Hapus Jadwal ---
    // Pastikan metode ini ada dan bersifat public
    public function editSelectedSchedule($scheduleId)
    {
        $this->dispatch('editSchedule', schedule: $scheduleId)->to('managers.maintenance.maintenance-schedules');
    }

    public function confirmDeleteScheduleOnParent($scheduleId)
    {
        $this->dispatch('confirmDeleteSchedule', id: $scheduleId)->to('managers.maintenance.maintenance-schedules');
    }
}