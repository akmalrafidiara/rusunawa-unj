<?php

namespace App\Livewire\Managers;

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
use Illuminate\Validation\ValidationException;
use Spatie\LivewireFilepond\WithFilePond;

class Maintenance extends Component
{
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // --- Properties for Maintenance Schedule Form ---
    public $scheduleIdBeingEdited = null;
    public $scheduleUnitId = '';
    public $scheduleFrequencyMonths = '';
    public $scheduleNotes = '';
    public $scheduleStatus = MaintenanceScheduleStatus::SCHEDULED->value;
    public $scheduleNextDueDate;

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
    public $showScheduleModal = false;
    public $showRecordModal = false;
    public $showRecordDetailModal = false;
    public $modalType = '';
    public $currentRecordIdForDetail = null;
    public $currentScheduleId = null;
    public $selectedSchedule = null; // <<== Properti baru
    public $relatedRecords = null; // <<== Properti baru

    public $tab = 'schedules';

    // --- Filter properties ---
    public $search = '';
    public $filterScheduleStatus = '';

    // --- Options for dropdowns ---
    public $unitOptions = [];
    public $allAcUnitOptions = [];
    public $scheduleStatusOptions = [];
    public $recordTypeOptions = [];
    public $recordStatusOptions = [];
    public $frequencyOptions = [];
    public $currentScheduleUnitOption = [];

    // --- Pagination and sorting ---
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterScheduleStatus' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
        'tab' => ['except' => 'schedules'],
    ];

    protected $messages = [
        'scheduleUnitId.required' => 'Unit wajib dipilih.',
        'scheduleUnitId.exists' => 'Unit yang dipilih tidak valid.',
        'scheduleUnitId.unique' => 'Unit ini sudah memiliki jadwal pemeliharaan rutin.',
        'scheduleFrequencyMonths.required' => 'Frekuensi pemeliharaan wajib diisi.',
        'scheduleFrequencyMonths.integer' => 'Frekuensi pemeliharaan harus berupa angka.',
        'scheduleFrequencyMonths.min' => 'Frekuensi pemeliharaan minimal harus :min bulan.',
        'scheduleNextDueDate.required' => 'Tanggal jatuh tempo berikutnya wajib diisi.',
        'scheduleNextDueDate.date' => 'Tanggal jatuh tempo berikutnya harus berupa tanggal yang valid.',
        'scheduleNotes.string' => 'Catatan harus berupa teks.',
        'scheduleNotes.max' => 'Catatan tidak boleh lebih dari :max karakter.',

        'recordUnitId.required' => 'Unit wajib dipilih.',
        'recordUnitId.exists' => 'Unit yang dipilih tidak valid.',
        'recordType.required' => 'Tipe pemeliharaan wajib dipilih.',
        'recordType.in' => 'Tipe pemeliharaan tidak valid.',
        'recordScheduledDate.required' => 'Tanggal terjadwal wajib diisi.',
        'recordScheduledDate.date' => 'Tanggal terjadwal harus berupa tanggal yang valid.',
        'recordCompletionDate.date' => 'Tanggal penyelesaian harus berupa tanggal yang valid.',
        'recordCompletionDate.after_or_equal' => 'Tanggal penyelesaian tidak boleh lebih awal dari tanggal terjadwal.',
        'recordNotes.string' => 'Catatan harus berupa teks.',
        'recordNotes.max' => 'Catatan tidak boleh lebih dari :max karakter.',
        'recordAttachments.*.file' => 'File harus berupa file yang valid.',
        'recordAttachments.*.mimes' => 'Format file yang diizinkan adalah JPG, JPEG, PNG, GIF, atau PDF.',
        'recordAttachments.*.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
    ];

    /**
     * Rules specific to Maintenance Schedule form.
     */
    protected function scheduleRules(): array
    {
        return [
            'scheduleUnitId' => [
                'required',
                'exists:units,id',
                Rule::unique('maintenance_schedules', 'unit_id')->ignore($this->scheduleIdBeingEdited, 'id'),
            ],
            'scheduleFrequencyMonths' => 'required|integer|min:1',
            'scheduleNextDueDate' => 'required|date',
            'scheduleNotes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Rules specific to Maintenance Record form.
     */
    protected function recordRules(): array
    {
        return [
            'recordUnitId' => 'required|exists:units,id',
            'recordMaintenanceScheduleId' => 'nullable|exists:maintenance_schedules,id',
            'recordType' => ['required', Rule::in(MaintenanceRecordType::values())],
            'recordScheduledDate' => 'required|date',
            'recordCompletionDate' => 'nullable|date',
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
            ->map(fn ($unit) => [
                'value' => $unit->id,
                'label' => 'Kamar ' . $unit->room_number . (optional($unit->unitCluster)->name ? ' - ' . $unit->unitCluster->name : '')
            ])
            ->toArray();

        $this->unitOptions = Unit::select('id', 'room_number', 'unit_cluster_id')
            ->with(['unitCluster', 'unitType'])
            ->whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))
            ->doesntHave('maintenanceSchedule')
            ->orderBy('room_number')
            ->get()
            ->map(fn ($unit) => [
                'value' => $unit->id,
                'label' => 'Kamar ' . $unit->room_number . (optional($unit->unitCluster)->name ? ' - ' . $unit->unitCluster->name : '')
            ])
            ->toArray();

        $this->scheduleStatusOptions = MaintenanceScheduleStatus::options();
        $this->recordTypeOptions = MaintenanceRecordType::options();
        $this->recordStatusOptions = MaintenanceRecordStatus::options();

        $this->frequencyOptions = collect([1, 2, 3, 4, 6, 12])->map(fn ($i) => [
            'value' => $i,
            'label' => $i . ' Bulan Sekali',
        ])->toArray();
    }

    public function render()
    {
        $maintenanceUnitIds = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->pluck('id');

        $schedules = MaintenanceSchedule::query()
            ->whereIn('unit_id', $maintenanceUnitIds)
            ->when($this->search, function ($query) {
                $searchTerm = strtolower($this->search);
                $query->whereHas('unit', fn ($q) =>
                    $q->whereRaw('LOWER(room_number) LIKE ?', ['%' . $searchTerm . '%'])
                      ->orWhereRaw('LOWER(CONCAT("Kamar ", room_number)) LIKE ?', ['%' . $searchTerm . '%'])
                )
                ->orWhereHas('unit.unitCluster', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']));
            })
            ->when($this->filterScheduleStatus, fn ($q) => $q->where('status', $this->filterScheduleStatus))
            ->with(['unit.unitCluster'])
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage, pageName: 'schedulePage');

        if (is_null($this->currentScheduleId) && $schedules->isNotEmpty()) {
            $this->currentScheduleId = $schedules->first()->id;
        } elseif (!is_null($this->currentScheduleId) && $schedules->where('id', $this->currentScheduleId)->isEmpty()) {
            $this->currentScheduleId = $schedules->isNotEmpty() ? $schedules->first()->id : null;
        }

        // --- Logika Pembaruan Status Jadwal ---
        foreach ($schedules as $schedule) {
            $now = Carbon::now();
            $nextDueDate = Carbon::parse($schedule->next_due_date);

            if ($now->isAfter($nextDueDate->endOfDay())) {
                if ($schedule->status->value !== MaintenanceScheduleStatus::OVERDUE->value) {
                    $schedule->status = MaintenanceScheduleStatus::OVERDUE;
                    $schedule->save();
                }
            } elseif ($now->diffInDays($nextDueDate, false) <= 7 && $now->isBefore($nextDueDate)) {
                if ($schedule->status->value !== MaintenanceScheduleStatus::UPCOMING->value) {
                    $schedule->status = MaintenanceScheduleStatus::UPCOMING;
                    $schedule->save();
                }
            } else {
                if ($schedule->status->value !== MaintenanceScheduleStatus::SCHEDULED->value) {
                    $schedule->status = MaintenanceScheduleStatus::SCHEDULED;
                    $schedule->save();
                }
            }
        }

        $records = MaintenanceRecord::query()
            ->whereIn('unit_id', $maintenanceUnitIds)
            ->when($this->search, function ($query) {
                $searchTerm = strtolower($this->search);
                $query->whereHas('unit', fn ($q) =>
                    $q->whereRaw('LOWER(room_number) LIKE ?', ['%' . $searchTerm . '%'])
                      ->orWhereRaw('LOWER(CONCAT("Kamar ", room_number)) LIKE ?', ['%' . $searchTerm . '%'])
                )
                ->orWhereHas('unit.unitCluster', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']));
            })
            ->with(['unit.unitCluster', 'maintenanceSchedule', 'attachments'])
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage, pageName: 'recordPage');

        // Pindahkan logika ini ke sini agar properti tersedia di Blade
        $this->selectedSchedule = $schedules->firstWhere('id', $this->currentScheduleId);
        $this->relatedRecords = \App\Models\MaintenanceRecord::where(function($query) {
                $query->where('maintenance_schedule_id', $this->currentScheduleId);
                if ($this->selectedSchedule) {
                    $query->orWhere(function($q) {
                        $q->where('unit_id', $this->selectedSchedule->unit_id)
                          ->where('type', \App\Enums\MaintenanceRecordType::URGENT->value);
                    });
                }
            })
            ->orderBy('completion_date', 'desc')
            ->get();


        return view('livewire.managers.oprations.maintenance.index', compact('schedules', 'records'));
    }

    // --- Schedule Methods ---
    public function createSchedule()
    {
        $this->resetScheduleForm();
        $this->modalType = 'create_schedule';
        $this->showScheduleModal = true;
        $this->scheduleStatus = MaintenanceScheduleStatus::SCHEDULED->value;
        $this->scheduleNotes = null;
        $this->currentScheduleUnitOption = [];
    }

    public function editSchedule(MaintenanceSchedule $schedule)
    {
        $this->scheduleIdBeingEdited = $schedule->id;
        $this->scheduleUnitId = $schedule->unit_id;
        $this->scheduleNextDueDate = $schedule->next_due_date->format('Y-m-d');
        $this->scheduleFrequencyMonths = $schedule->frequency_months;
        $this->scheduleNotes = $schedule->notes;
        $this->scheduleStatus = $schedule->status->value;

        // Set the currentScheduleUnitOption for display in edit mode
        $this->currentScheduleUnitOption = [
            [
                'value' => $schedule->unit->id,
                'label' => 'Kamar ' . $schedule->unit->room_number . (optional($schedule->unit->unitCluster)->name ? ' - ' . $schedule->unit->unitCluster->name : '')
            ]
        ];
        $this->modalType = 'edit_schedule';
        $this->showScheduleModal = true;
    }

    public function saveSchedule()
    {
        try {
            $this->validate($this->scheduleRules());

            $data = [
                'unit_id' => $this->scheduleUnitId,
                'frequency_months' => $this->scheduleFrequencyMonths,
                'status' => MaintenanceScheduleStatus::SCHEDULED->value,
                'notes' => $this->scheduleNotes,
            ];

            if ($this->scheduleIdBeingEdited) {
                $schedule = MaintenanceSchedule::find($this->scheduleIdBeingEdited);
                if ($schedule) {
                    $schedule->frequency_months = $this->scheduleFrequencyMonths;
                    $schedule->notes = $this->scheduleNotes;
                    $schedule->save();
                }
            } else {
                $data['next_due_date'] = $this->scheduleNextDueDate;
                MaintenanceSchedule::create($data);
            }

            LivewireAlert::title($this->scheduleIdBeingEdited ? 'Jadwal pemeliharaan berhasil diperbarui.' : 'Jadwal pemeliharaan berhasil ditambahkan.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();

            $this->resetScheduleForm();
            $this->showScheduleModal = false;
            $this->mount();
        } catch (ValidationException $e) {
            Log::error('Validation error saving maintenance schedule: ' . $e->getMessage(), [
                'errors' => $e->errors(),
                'livewire_properties' => $this->all(),
            ]);
            LivewireAlert::error('Gagal menyimpan jadwal pemeliharaan. Periksa kembali input Anda.')->toast()->position('top-end')->show();
        } catch (Throwable $e) {
            Log::error('General error saving maintenance schedule: ' . $e->getMessage(), [
                'exception' => $e,
                'livewire_properties' => $this->all(),
            ]);
            LivewireAlert::error('Gagal menyimpan jadwal pemeliharaan. Silakan coba lagi atau hubungi administrator.')->toast()->position('top-end')->show();
        }
    }

    public function confirmDeleteSchedule($id)
    {
        LivewireAlert::title('Hapus Jadwal Pemeliharaan?')
            ->text('Apakah Anda yakin ingin menghapus jadwal pemeliharaan ini? Ini juga akan menghapus semua riwayat pemeliharaan rutin terkait.')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteSchedule', ['id' => $id])
            ->show();
    }

    public function deleteSchedule($data)
    {
        try {
            $schedule = MaintenanceSchedule::find($data['id']);
            if ($schedule) {
                $schedule->delete();
                LivewireAlert::success('Jadwal pemeliharaan berhasil dihapus.')->toast()->position('top-end');
                $this->mount();
            }
        } catch (Throwable $e) {
            Log::error('Error deleting maintenance schedule: ' . $e->getMessage(), ['exception' => $e]);
            LivewireAlert::error('Gagal menghapus jadwal pemeliharaan.')->toast()->position('top-end')->show();
        }
    }

    private function resetScheduleForm()
    {
        $this->resetValidation();
        $this->reset([
            'scheduleIdBeingEdited',
            'scheduleUnitId',
            'scheduleFrequencyMonths',
            'scheduleNextDueDate',
            'scheduleNotes',
            'scheduleStatus',
        ]);
        $this->scheduleStatus = MaintenanceScheduleStatus::SCHEDULED->value;
        $this->scheduleNotes = null;
        $this->currentScheduleUnitOption = [];
    }

    // --- Record Methods ---
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
        $this->currentRecordIdForDetail = $record->id;
        $this->recordUnitId = $record->unit_id;
        $this->recordMaintenanceScheduleId = $record->maintenance_schedule_id;
        $this->recordType = $record->type->value;
        $this->recordScheduledDate = $record->scheduled_date->format('Y-m-d');
        $this->recordCompletionDate = $record->completion_date?->format('d F Y');
        $this->recordStatus = $record->status->value;
        $this->recordNotes = $record->notes;
        $this->existingRecordAttachments = $record->attachments->toArray();
        $scheduled = Carbon::parse($this->recordScheduledDate)->startOfDay();
        $completed = $record->completion_date ? Carbon::parse($record->completionDate)->startOfDay() : null;
        $this->isLate = $completed ? $completed->greaterThan($scheduled) : false;

        $this->modalType = 'detail_record';
        $this->showRecordDetailModal = true;
    }

    public function saveRecord()
    {
        try {
            $this->validate($this->recordRules());

            $data = [
                'unit_id' => $this->recordUnitId,
                'maintenance_schedule_id' => $this->recordMaintenanceScheduleId,
                'type' => $this->recordType,
                'scheduled_date' => $this->recordScheduledDate,
                'notes' => $this->recordNotes,
            ];

            // --- Logika Penentuan Status Rekaman ---
            if ($this->recordType === MaintenanceRecordType::URGENT->value) {
                // Untuk rekaman darurat, status selalu URGENT, tidak peduli tanggal penyelesaian
                $data['status'] = MaintenanceRecordStatus::URGENT->value;
                $data['is_late'] = false; // Konsep terlambat tidak berlaku untuk darurat

                if (!empty($this->recordCompletionDate)) {
                    $data['completion_date'] = Carbon::parse($this->recordCompletionDate)->startOfDay();
                } else {
                    $data['completion_date'] = null;
                }
            } else { // Untuk rekaman RUTIN
                $scheduledDate = Carbon::parse($this->recordScheduledDate)->startOfDay();
                if (!empty($this->recordCompletionDate)) {
                    $completionDate = Carbon::parse($this->recordCompletionDate)->startOfDay();

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
                } else { // Jika tanggal penyelesaian kosong untuk rutin
                    $data['completion_date'] = null;
                    $data['is_late'] = false;
                    $data['status'] = MaintenanceRecordStatus::SCHEDULED->value;
                }
            }
            // --- Akhir Logika Penentuan Status ---


            $record = MaintenanceRecord::updateOrCreate(
                ['id' => $this->recordIdBeingEdited],
                $data
            );

            // Handle attachment deletions
            $this->handleAttachmentDeletions($record);
            // Handle new attachment uploads
            $this->handleAttachmentUploads($record);

            if ($record->type->value === MaintenanceRecordType::ROUTINE->value && $record->maintenanceSchedule) {
                $schedule = $record->maintenanceSchedule;
                if ($record->completion_date) {
                    $schedule->next_due_date = Carbon::parse($record->completion_date)->addMonths($schedule->frequency_months);
                    $schedule->last_completed_at = $record->completion_date;
                    $schedule->status = MaintenanceScheduleStatus::SCHEDULED;
                } else if ($record->status->value === MaintenanceRecordStatus::POSTPONED->value) {
                    $schedule->status = MaintenanceScheduleStatus::POSTPONED;
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
        } catch (ValidationException $e) {
            Log::error('Validation error saving maintenance record: ' . $e->getMessage(), [
                'errors' => $e->errors(),
                'livewire_properties' => $this->all(),
            ]);
            LivewireAlert::error('Gagal menyimpan rekaman pemeliharaan. Periksa kembali input Anda.')->toast()->position('top-end')->show();
        } catch (Throwable $e) {
            Log::error('General error saving maintenance record: ' . $e->getMessage(), [
                'exception' => $e,
                'livewire_properties' => $this->all(),
            ]);
            LivewireAlert::error('Gagal menyimpan rekaman pemeliharaan. Silakan coba lagi atau hubungi administrator.')->toast()->position('top-end')->show();
        }
    }

    /**
     * Remove an attachment from the attachments array for deletion.
     *
     * @param int $attachmentId
     */
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

    /**
     * Remove a newly uploaded attachment before saving.
     *
     * @param int $index
     */
    public function removeNewAttachment($index)
    {
        if (isset($this->recordAttachments[$index])) {
            unset($this->recordAttachments[$index]);
            $this->recordAttachments = array_values($this->recordAttachments); // Re-index array
            LivewireAlert::success('File berhasil dihapus dari daftar unggah.')->toast()->position('top-end');
        }
    }

    /**
     * Handles deleting attachments marked for removal.
     * @param MaintenanceRecord $record
     */
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

    /**
     * Handles newly uploaded attachments.
     * @param MaintenanceRecord $record
     */
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
        } catch (Throwable $e) {
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

    // --- Utility Methods ---
    public function updated($propertyName)
    {
        if (Str::startsWith($propertyName, 'schedule')) {
            $this->validateOnly($propertyName, $this->scheduleRules());
        } elseif (Str::startsWith($propertyName, 'record')) {
            $this->validateOnly($propertyName, $this->recordRules());
        }
    }

    public function updatedTab()
    {
        $this->resetPage('schedulePage');
        $this->resetPage('recordPage');
    }
}