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
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleUser;
use Illuminate\Pagination\LengthAwarePaginator;

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
    public $originalScheduleStatus = '';
    public $originalScheduleNextDueDate = '';

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
    public $selectedSchedule = null;

    // Private property to hold the paginator instance for related records
    private $relatedRecordsPaginator = null;

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
    public $orderBy = 'next_due_date';
    public $sort = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterScheduleStatus' => ['except' => ''],
        'perPage' => ['except' => 5],
        'orderBy' => ['except' => 'next_due_date'],
        'sort' => ['except' => 'asc'],
        'tab' => ['except' => 'schedules'],
        'currentScheduleId' => ['except' => null],
        'relatedRecordsPage' => ['except' => 1, 'as' => 'recordHistoryPage'],
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
        'scheduleNextDueDate.after_or_equal' => 'Tanggal penundaan harus hari ini atau di masa depan.',
        'scheduleNextDueDate.different' => 'Tanggal penundaan tidak boleh sama dengan tanggal pemeliharaan yang sudah dijadwalkan.', // New message
        'scheduleNotes.string' => 'Catatan harus berupa teks.',
        'scheduleNotes.max' => 'Catatan tidak boleh lebih dari :max karakter.',
        'scheduleStatus.required' => 'Status jadwal wajib dipilih.',
        'scheduleStatus.in' => 'Status jadwal tidak valid.',

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
            'scheduleNextDueDate' => [
                'required',
                'date',
                // If editing and status is postponed, ensure it's a future date (today or later)
                // AND different from the original scheduled date.
                Rule::when($this->modalType === 'edit_schedule' && $this->scheduleStatus === MaintenanceScheduleStatus::POSTPONED->value, [
                    'after_or_equal:today',
                    'different:originalScheduleNextDueDate',
                ]),
            ],
            'scheduleNotes' => 'nullable|string|max:500',
            // Status is required only when editing, as it's hidden for creation
            'scheduleStatus' => [
                Rule::when($this->modalType === 'edit_schedule', ['required', Rule::in(MaintenanceScheduleStatus::values())]),
                Rule::when($this->modalType === 'create_schedule', 'nullable'), 
            ],
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

        // Initial population for unitOptions for 'create_schedule'
        $this->updateUnitOptionsForCreateSchedule();

        $this->scheduleStatusOptions = MaintenanceScheduleStatus::options();
        $this->recordTypeOptions = MaintenanceRecordType::options();
        $this->recordStatusOptions = MaintenanceRecordStatus::options();

        // Updated: Generate frequency options from 1 to 12
        $this->frequencyOptions = collect(range(1, 12))->map(fn($i) => [
            'value' => $i,
            'label' => $i . ' Bulan Sekali',
        ])->toArray();
    }

    // New method to update unitOptions specifically for create schedule form
    private function updateUnitOptionsForCreateSchedule()
    {
        $this->unitOptions = Unit::select('id', 'room_number', 'unit_cluster_id')
            ->with(['unitCluster', 'unitType'])
            ->whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))
            ->doesntHave('maintenanceSchedule')
            ->orderBy('room_number')
            ->get()
            ->map(fn($unit) => [
                'value' => $unit->id,
                'label' => 'Kamar ' . $unit->room_number . (optional($unit->unitCluster)->name ? ' - ' . $unit->unitCluster->name : '')
            ])
            ->toArray();
    }

    public function render()
{
    $maintenanceUnitIds = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->pluck('id');

    $schedulesQuery = MaintenanceSchedule::query()
        ->whereIn('unit_id', $maintenanceUnitIds)
        // Filter status dulu sebelum search
        ->when($this->filterScheduleStatus, fn($q) => $q->where('status', $this->filterScheduleStatus))
        // Baru kemudian search dalam hasil yang sudah difilter
        ->when($this->search, function ($query) {
            $searchTerm = strtolower($this->search);
            $query->whereHas(
                'unit',
                fn($q) =>
                $q->whereRaw('LOWER(room_number) LIKE ?', ['%' . $searchTerm . '%'])
                    ->orWhereRaw('LOWER(CONCAT("Kamar ", room_number)) LIKE ?', ['%' . $searchTerm . '%'])
            )
                ->orWhereHas('unit.unitCluster', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']));
        })
        ->with(['unit.unitCluster'])
        ->orderBy($this->orderBy, $this->sort);

    $schedules = $schedulesQuery->paginate($this->perPage, pageName: 'schedulePage');

    // Logic to correctly set selectedSchedule based on currentScheduleId
    if ($this->currentScheduleId) {
        $this->selectedSchedule = MaintenanceSchedule::with(['unit.unitCluster'])->find($this->currentScheduleId);
        // If selectedSchedule is null (e.g., deleted or not found), clear currentScheduleId
        if (!$this->selectedSchedule) {
            $this->currentScheduleId = null;
        }
    }

    // If no specific schedule is selected or the previously selected one was not found,
    // and there are schedules on the current page, default to the first one on the current page.
    if (is_null($this->currentScheduleId) && $schedules->isNotEmpty()) {
        $this->currentScheduleId = $schedules->first()->id;
        $this->selectedSchedule = $schedules->first();
    }

    // --- Logika Pembaruan Status Jadwal Otomatis ---
    foreach ($schedules as $schedule) {
        $now = Carbon::now();
        $nextDueDate = Carbon::parse($schedule->next_due_date);

        // Store old status to check if it changed to SCHEDULED
        $oldStatus = $schedule->status->value;

        // If schedule is explicitly postponed, check for upcoming status based on postpone date
        if ($oldStatus === MaintenanceScheduleStatus::POSTPONED->value) {
            // Change to UPCOMING if postponed date is within 7 days and is before the actual due date
            if ($now->diffInDays($nextDueDate, false) <= 7 && $now->isBefore($nextDueDate)) {
                if ($schedule->status->value !== MaintenanceScheduleStatus::UPCOMING->value) {
                    $schedule->status = MaintenanceScheduleStatus::UPCOMING;
                    $schedule->save();
                }
            }
            // Continue to next schedule if it's still postponed and not upcoming
            continue;
        }

        // Normal status updates for non-postponed schedules
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
            // If it's becoming SCHEDULED automatically AND it wasn't SCHEDULED before, clear notes
            if ($schedule->status->value !== MaintenanceScheduleStatus::SCHEDULED->value) {
                $schedule->status = MaintenanceScheduleStatus::SCHEDULED;
                // Clear notes ONLY if status was NOT SCHEDULED and is now BECOMING SCHEDULED
                if ($oldStatus !== MaintenanceScheduleStatus::SCHEDULED->value && $schedule->status->value === MaintenanceScheduleStatus::SCHEDULED->value) {
                    $schedule->notes = null;
                }
                $schedule->save();
            }
        }
    }

    $records = MaintenanceRecord::query()
        ->whereIn('unit_id', $maintenanceUnitIds)
        ->when($this->search, function ($query) {
            $searchTerm = strtolower($this->search);
            $query->whereHas(
                'unit',
                fn($q) =>
                $q->whereRaw('LOWER(room_number) LIKE ?', ['%' . $searchTerm . '%'])
                    ->orWhereRaw('LOWER(CONCAT("Kamar ", room_number)) LIKE ?', ['%' . $searchTerm . '%'])
            )
                ->orWhereHas('unit.unitCluster', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']));
        })
        ->with(['unit.unitCluster', 'maintenanceSchedule', 'attachments'])
        ->orderBy('created_at', 'desc') // Explicitly set order for records
        ->paginate($this->perPage, pageName: 'recordPage'); // Pagination for records

    // Assign paginated related records to a private property
    if ($this->selectedSchedule) {
        $this->relatedRecordsPaginator = \App\Models\MaintenanceRecord::where(function ($query) {
            $query->where('maintenance_schedule_id', $this->selectedSchedule->id);
            $query->orWhere(function ($q) {
                $q->where('unit_id', $this->selectedSchedule->unit_id)
                    ->where('type', \App\Enums\MaintenanceRecordType::URGENT->value);
            });
        })
            ->orderBy('completion_date', 'desc')
            ->paginate(5, pageName: 'relatedRecordsPage'); // Paginate related records with a distinct page name
    } else {
        // If no selectedSchedule, provide an empty paginator to avoid errors in the blade.
        $this->relatedRecordsPaginator = new LengthAwarePaginator([], 0, 5, 1, ['pageName' => 'relatedRecordsPage']);
    }

    // Assign private property to a local variable for compact() to pick it up
    $relatedRecordsPaginator = $this->relatedRecordsPaginator;

    // Pass role information to the view
    $is_admin_user = Auth::user()->hasRole(RoleUser::ADMIN->value);
    $is_head_of_rusunawa_user = Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value);

    return view('livewire.managers.oprations.maintenance.index', compact('schedules', 'records', 'is_admin_user', 'is_head_of_rusunawa_user', 'relatedRecordsPaginator'));
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
        $this->updateUnitOptionsForCreateSchedule();
    }

    public function editSchedule(MaintenanceSchedule $schedule)
    {
        $this->resetValidation();
        $this->scheduleIdBeingEdited = $schedule->id;
        $this->scheduleUnitId = $schedule->unit_id;
        $this->scheduleNextDueDate = $schedule->next_due_date->format('Y-m-d');
        $this->scheduleFrequencyMonths = $schedule->frequency_months;
        $this->scheduleNotes = $schedule->notes;
        $this->scheduleStatus = $schedule->status->value;
        $this->originalScheduleStatus = $schedule->status->value;
        $this->originalScheduleNextDueDate = $schedule->next_due_date->format('Y-m-d');

        $this->currentScheduleUnitOption = [
            [
                'value' => $schedule->unit->id,
                'label' => 'Kamar ' . $schedule->unit->room_number . (optional($schedule->unit->unitCluster)->name ? ' - ' . $schedule->unit->unitCluster->name : '')
            ]
        ];
        $this->modalType = 'edit_schedule';
        $this->showScheduleModal = true;
    }

    // New Livewire lifecycle hook to handle property updates
    public function updatedScheduleStatus($value)
    {
        // If status changes to Postponed, clear the next due date
        if ($value === MaintenanceScheduleStatus::POSTPONED->value) {
            $this->scheduleNextDueDate = null;
            $this->resetErrorBag('scheduleNextDueDate'); // Clear any validation errors specific to this field
        } else {
            // If status is changing away from Postponed AND next_due_date was cleared, restore original date
            if (is_null($this->scheduleNextDueDate) && !empty($this->originalScheduleNextDueDate)) {
                $this->scheduleNextDueDate = $this->originalScheduleNextDueDate;
            }
        }
    }

    public function saveSchedule()
    {
        $this->validate($this->scheduleRules());

        $data = [
            'unit_id' => $this->scheduleUnitId,
            'frequency_months' => $this->scheduleFrequencyMonths,
            'notes' => $this->scheduleNotes,
            'status' => $this->scheduleStatus,
        ];

        if ($this->scheduleIdBeingEdited) {
            $schedule = MaintenanceSchedule::find($this->scheduleIdBeingEdited);
            if ($schedule) {
                $schedule->frequency_months = $this->scheduleFrequencyMonths;
                $schedule->notes = $this->scheduleNotes;
                $schedule->next_due_date = $this->scheduleNextDueDate;
                $schedule->status = $this->scheduleStatus;
                $schedule->save();
            }
        } else {
            $data['next_due_date'] = $this->scheduleNextDueDate;
            $data['status'] = MaintenanceScheduleStatus::SCHEDULED->value;
            $schedule = MaintenanceSchedule::create($data);
        }

        $this->currentScheduleId = $schedule->id;
        $this->tab = 'schedules';

        // Calculate and set the correct page after saving
        $page = $this->getPageOfSchedule($this->currentScheduleId);
        $this->setPage($page, 'schedulePage');

        LivewireAlert::title($this->scheduleIdBeingEdited ? 'Jadwal pemeliharaan berhasil diperbarui.' : 'Jadwal pemeliharaan berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetScheduleForm();
        $this->showScheduleModal = false;
        $this->updateUnitOptionsForCreateSchedule();
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
        $schedule = MaintenanceSchedule::find($data['id']);
        if ($schedule) {
            $schedule->delete();
            LivewireAlert::success('Jadwal pemeliharaan berhasil dihapus.')->toast()->position('top-end');
            $this->mount();
            $this->updateUnitOptionsForCreateSchedule();
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
            'originalScheduleStatus',
            'originalScheduleNextDueDate',
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

        $scheduleIdToSelect = null;
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

            // Clear schedule notes ONLY if schedule status changed to SCHEDULED
            if ($oldScheduleStatus !== MaintenanceScheduleStatus::SCHEDULED->value && $schedule->status->value === MaintenanceScheduleStatus::SCHEDULED->value) {
                $schedule->notes = null;
            }
            $schedule->save();
            $scheduleIdToSelect = $schedule->id;
        } else if ($record->type->value === MaintenanceRecordType::URGENT->value && $record->unit->maintenanceSchedule) {
            // If it's an urgent record, try to select its unit's routine schedule if it exists
            $scheduleIdToSelect = $record->unit->maintenanceSchedule->id;
        }

        if ($scheduleIdToSelect) {
            $this->currentScheduleId = $scheduleIdToSelect;
            $this->tab = 'schedules';
            $page = $this->getPageOfSchedule($this->currentScheduleId);
            $this->setPage($page, 'schedulePage');
        } else {
            $this->tab = 'records'; 
            $this->currentScheduleId = null;
        }

        LivewireAlert::title($this->recordIdBeingEdited ? 'Rekaman pemeliharaan berhasil diperbarui.' : 'Rekaman pemeliharaan berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetRecordForm();
        $this->showRecordModal = false;
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
            $this->recordAttachments = array_values($this->recordAttachments);
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
        if (Str::startsWith($propertyName, 'schedule')) {
            $this->validateOnly($propertyName, $this->scheduleRules());
        } elseif (Str::startsWith($propertyName, 'record')) {
            $this->validateOnly($propertyName, $this->recordRules());
        }

        // Dimana saja search atau filterScheduleStatus diupdate, reset halaman yang relevan
        if ($propertyName === 'search' || $propertyName === 'filterScheduleStatus') {
            $this->resetPage('schedulePage');
            $this->resetPage('recordPage');  
            $this->resetPage('relatedRecordsPage'); 
        }
    }

    public function updatedTab()
    {
        $this->resetPage('schedulePage');
        $this->resetPage('recordPage');
        $this->resetPage('relatedRecordsPage');
    }

    /**
     * Helper to get the page number of a given schedule ID.
     */
    protected function getPageOfSchedule($scheduleId)
    {
        $maintenanceUnitIds = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->pluck('id');

        $allSchedules = MaintenanceSchedule::query()
            ->whereIn('unit_id', $maintenanceUnitIds)
            ->when($this->search, function ($query) {
                $searchTerm = strtolower($this->search);
                $query->whereHas(
                    'unit',
                    fn($q) =>
                    $q->whereRaw('LOWER(room_number) LIKE ?', ['%' . $searchTerm . '%'])
                        ->orWhereRaw('LOWER(CONCAT("Kamar ", room_number)) LIKE ?', ['%' . $searchTerm . '%'])
                )
                    ->orWhereHas('unit.unitCluster', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']));
            })
            ->when($this->filterScheduleStatus, fn($q) => $q->where('status', $this->filterScheduleStatus))
            ->orderBy($this->orderBy, $this->sort)
            ->pluck('id')
            ->toArray();

        $index = array_search($scheduleId, $allSchedules);
        if ($index !== false) {
            return floor($index / $this->perPage) + 1;
        }
        return 1;
    }
}