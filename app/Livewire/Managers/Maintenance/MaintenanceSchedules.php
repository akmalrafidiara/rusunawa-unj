<?php

namespace App\Livewire\Managers\Maintenance;

use App\Enums\MaintenanceScheduleStatus;
use App\Models\MaintenanceSchedule;
use App\Models\Unit;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleUser;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Livewire;

class MaintenanceSchedules extends Component
{
    use WithPagination;

    // --- Properties for Maintenance Schedule Form ---
    public $scheduleIdBeingEdited = null;
    public $scheduleUnitId = '';
    public $scheduleFrequencyMonths = '';
    public $scheduleNotes = '';
    public $scheduleStatus = MaintenanceScheduleStatus::SCHEDULED->value;
    public $scheduleNextDueDate;
    public $originalScheduleStatus = '';
    public $originalScheduleNextDueDate = '';

    // --- General UI properties ---
    public $showScheduleModal = false;
    public $modalType = ''; // 'create_schedule' or 'edit_schedule'

    // --- Filter properties ---
    public $search = '';
    public $filterScheduleStatus = '';

    // --- Options for dropdowns ---
    public $unitOptions = [];
    public $allAcUnitOptions = []; // Digunakan untuk display nama unit di form edit/detail
    public $scheduleStatusOptions = [];
    public $frequencyOptions = [];

    // --- Pagination and sorting ---
    public $perPage = 10;
    public $orderBy = 'next_due_date';
    public $sort = 'asc';

    // Property untuk komunikasi dengan MaintenanceRecords
    #[Url(as: 'selectedScheduleId', keep: true)]
    public $selectedScheduleId = null;
    

    protected $queryString = [
        'search' => ['except' => ''],
        'filterScheduleStatus' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'next_due_date'],
        'sort' => ['except' => 'asc'],
        'selectedScheduleId' => ['except' => null], // Query string untuk schedule yang dipilih
        'schedulePage' => ['except' => 1, 'as' => 'jadwalPage'], // Untuk paginasi jadwal
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
        'scheduleNextDueDate.different' => 'Tanggal penundaan tidak boleh sama dengan tanggal pemeliharaan yang sudah dijadwalkan.',
        'scheduleNotes.string' => 'Catatan harus berupa teks.',
        'scheduleNotes.max' => 'Catatan tidak boleh lebih dari :max karakter.',
        'scheduleStatus.required' => 'Status jadwal wajib dipilih.',
        'scheduleStatus.in' => 'Status jadwal tidak valid.',
    ];

    protected function rules(): array
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
                Rule::when($this->modalType === 'edit_schedule' && $this->scheduleStatus === MaintenanceScheduleStatus::POSTPONED->value, [
                    'after_or_equal:today',
                    'different:originalScheduleNextDueDate',
                ]),
            ],
            'scheduleNotes' => 'nullable|string|max:500',
            'scheduleStatus' => [
                Rule::when($this->modalType === 'edit_schedule', ['required', Rule::in(MaintenanceScheduleStatus::values())]),
                Rule::when($this->modalType === 'create_schedule', 'nullable'),
            ],
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

        $this->updateUnitOptionsForCreateSchedule();
        $this->scheduleStatusOptions = MaintenanceScheduleStatus::options();
        $this->frequencyOptions = collect(range(1, 12))->map(fn($i) => [
            'value' => $i,
            'label' => $i . ' Bulan Sekali',
        ])->toArray();

        if ($this->selectedScheduleId) {
            // 3. Kirim event ke browser untuk memberitahu ada item yang dipilih
            $this->dispatch('schedule-selected', id: $this->selectedScheduleId);
        }
    }

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
            ->when($this->filterScheduleStatus, fn($q) => $q->where('status', $this->filterScheduleStatus))
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

        // Jika halaman saat ini kosong dan ini bukan halaman pertama, coba reset ke halaman pertama.
    if ($schedules->isEmpty() && $schedules->currentPage() > 1) {
        $this->resetPage('schedulePage');
        $schedules = $schedulesQuery->paginate($this->perPage, pageName: 'schedulePage');
    }

    // Jika setelah itu masih kosong, maka benar-benar tidak ada data.
    if ($schedules->isEmpty()) {
        $this->selectedScheduleId = null;
    }

        // Dispatch event to MaintenanceRecords component when selectedScheduleId changes
        $this->dispatch('scheduleSelected', scheduleId: $this->selectedScheduleId);


        // --- Logika Pembaruan Status Jadwal Otomatis ---
        foreach ($schedules as $schedule) {
            $now = Carbon::now();
            $nextDueDate = Carbon::parse($schedule->next_due_date);
            $oldStatus = $schedule->status->value;

            if ($oldStatus === MaintenanceScheduleStatus::POSTPONED->value) {
                if ($now->diffInDays($nextDueDate, false) <= 7 && $now->isBefore($nextDueDate)) {
                    if ($schedule->status->value !== MaintenanceScheduleStatus::UPCOMING->value) {
                        $schedule->status = MaintenanceScheduleStatus::UPCOMING;
                        $schedule->save();
                    }
                }
                continue;
            }

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
                    if ($oldStatus !== MaintenanceScheduleStatus::SCHEDULED->value && $schedule->status->value === MaintenanceScheduleStatus::SCHEDULED->value) {
                        $schedule->notes = null;
                    }
                    $schedule->save();
                }
            }
        }

        $is_admin_user = Auth::user()->hasRole(RoleUser::ADMIN->value);
        $is_head_of_rusunawa_user = Auth::user()->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value);

        $scheduleStatusOptions = $this->scheduleStatusOptions; 
        
        return view('livewire.managers.oprations.maintenance.maintenance-schedules.index', compact('schedules', 'is_admin_user', 'is_head_of_rusunawa_user', 'scheduleStatusOptions'));
    }

    public function createSchedule()
    {
        $this->resetScheduleForm();
        $this->modalType = 'create_schedule';
        $this->showScheduleModal = true;
        $this->scheduleStatus = MaintenanceScheduleStatus::SCHEDULED->value;
        $this->scheduleNotes = null;
        $this->updateUnitOptionsForCreateSchedule();
    }

    #[On('editSchedule')]
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
        $this->modalType = 'edit_schedule';
        $this->showScheduleModal = true;
    }

    public function updatedScheduleStatus($value)
    {
        if ($value === MaintenanceScheduleStatus::POSTPONED->value) {
            $this->scheduleNextDueDate = null;
            $this->resetErrorBag('scheduleNextDueDate');
        } else {
            if (is_null($this->scheduleNextDueDate) && !empty($this->originalScheduleNextDueDate)) {
                $this->scheduleNextDueDate = $this->originalScheduleNextDueDate;
            }
        }
    }

    public function saveSchedule()
    {
        $this->validate($this->rules());

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

        $this->selectedScheduleId = $schedule->id; // Setel jadwal yang baru dibuat/diedit sebagai yang terpilih
        $page = $this->getPageOfSchedule($this->selectedScheduleId);
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

    #[On('confirmDeleteSchedule')]
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
            $unitId = $schedule->unit_id;

            $recordsToDelete = $schedule->maintenanceRecords()->get();
            // Juga hapus record Urgent yang terkait dengan unit ini
            $urgentRecords = \App\Models\MaintenanceRecord::where('unit_id', $unitId)
                ->where('type', \App\Enums\MaintenanceRecordType::URGENT->value)
                ->get();
            $recordsToDelete = $recordsToDelete->merge($urgentRecords)->unique('id');

            foreach ($recordsToDelete as $record) {
                foreach ($record->attachments as $attachment) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->path);
                    $attachment->delete();
                }
                $record->delete();
            }

            $schedule->delete();

            LivewireAlert::title('Jadwal pemeliharaan berhasil dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
            
            // Redirect server-side untuk memastikan halaman bersih
            return redirect()->route('maintenance');
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
        $this->updateUnitOptionsForCreateSchedule(); // Panggil ini juga di sini
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'schedule')) {
            $this->validateOnly($propertyName, $this->rules());
        }

        if ($propertyName === 'search' || $propertyName === 'filterScheduleStatus') {
            $this->resetPage('schedulePage');
        }
    }

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