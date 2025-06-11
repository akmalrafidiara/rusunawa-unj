<?php

namespace App\Livewire\Managers;

use App\Enums\GenderAllowed;
use App\Enums\UnitStatus;
use App\Exports\UnitsExport;
use App\Models\Unit as UnitModel;
use App\Models\UnitCluster;
use App\Models\UnitType;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class Unit extends Component
{
    // Traits
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public
        $roomNumber,
        $capacity,
        $virtualAccountNumber,
        $genderAllowed,
        $status,
        $unitTypeId,
        $unitClusterId,
        $createdAt,
        $updatedAt,
        $unitTypeName,
        $unitClusterName;

    // File upload properties
    public $unitImages = [];
    public $existingImages = [];
    public $imagesToDelete = [];

    // Options properties
    public $genderAllowedOptions;
    public $statusOptions;
    public $unitTypeOptions;
    public $unitClusterOptions;

    // Filter properties
    public $search = '';
    public $genderAllowedFilter = '';
    public $statusFilter = '';
    public $unitTypeFilter = '';
    public $unitClusterFilter = '';

    // Pagination and sorting properties
    public $perPage = 10;
    public $orderBy = 'room_number';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $modalType = '';
    public $unitIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'genderAllowedFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'unitTypeFilter' => ['except' => ''],
        'unitClusterFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        $this->genderAllowedOptions = GenderAllowed::options();
        $this->statusOptions = UnitStatus::options();
        $this->unitTypeOptions = UnitType::select('id', 'name')->get()->map(fn($unitType) => [
            'value' => $unitType->id,
            'label' => $unitType->name,
        ])->toArray();
        $this->unitClusterOptions = UnitCluster::select('id', 'name')->get()->map(fn($unitCluster) => [
            'value' => $unitCluster->id,
            'label' => $unitCluster->name,
        ])->toArray();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $units = UnitModel::query()
            ->when($this->search, fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->when($this->genderAllowedFilter, fn($q) => $q->where("gender_allowed", $this->genderAllowedFilter))
            ->when($this->statusFilter, fn($q) => $q->where("status", $this->statusFilter))
            ->when($this->unitTypeFilter, fn($q) => $q->where("unit_type_id", $this->unitTypeFilter))
            ->when($this->unitClusterFilter, fn($q) => $q->where("unit_cluster_id", $this->unitClusterFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        return view('livewire.managers.unit', compact('units'));
    }

    /**
     * Open modal for creating a new unit.
     */
    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Fill data for edit or detail modal.
     *
     * @param UnitModel $unit
     */
    protected function fillData(UnitModel $unit)
    {
        // Filling edit and modal
        $this->unitIdBeingEdited = $unit->id;
        $this->roomNumber = $unit->room_number;
        $this->capacity = $unit->capacity;
        $this->virtualAccountNumber = $unit->virtual_account_number;
        $this->genderAllowed = $unit->gender_allowed->value;
        $this->status = $unit->status->value;
        $this->unitTypeId = $unit->unit_type_id;
        $this->unitClusterId = $unit->unit_cluster_id;

        // Filling images data
        $this->existingImages = $unit->attachments()->get();

        // Reset image arrays
        $this->unitImages = [];
        $this->imagesToDelete = [];

        // Filling detail
        $this->unitTypeName = $unit->unitType->name;
        $this->unitClusterName = $unit->unitCluster->name;
        $this->createdAt = $unit->created_at;
        $this->updatedAt = $unit->updated_at;
    }

    /**
     * Open modal for editing or viewing details of a unit.
     *
     * @param UnitModel $unit
     */
    public function edit(UnitModel $unit)
    {
        $this->fillData($unit);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    /**
     * Open modal for viewing details of a unit.
     *
     * @param UnitModel $unit
     */
    public function detail(UnitModel $unit)
    {
        $this->fillData(unit: $unit);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * Rules for validation.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'roomNumber' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:3',
            'virtualAccountNumber' => 'nullable|numeric|digits_between:15,16',
            'genderAllowed' => [
                'required',
                Rule::in(GenderAllowed::values()),
            ],
            'status' => [
                'required',
                Rule::in(UnitStatus::values()),
            ],
            'unitTypeId' => 'required|exists:unit_types,id',
            'unitClusterId' => 'required|exists:unit_clusters,id',
            'unitImages.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per image
            'imagesToDelete' => 'array',
            'imagesToDelete.*' => 'exists:attachments,id', // Ensure the IDs exist in attachments
        ];
    }

    /**
     * Validate the form when a property is updated.
     * This method is called automatically (live) by Livewire when a property is updated.
     *
     * @param string $propertyName
     */
    public function updated($propertyName)
    {
        // Validate only the changed property if it exists in the rules
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    /**
     * Save the unit data.
     * This method is called when the form is submitted.
     */
    public function save()
    {
        // Validate all properties
        $this->validate($this->rules());

        // Prepare data for saving
        $data = [
            'room_number' => $this->roomNumber,
            'capacity' => $this->capacity,
            'virtual_account_number' => str_replace(' ', '', $this->virtualAccountNumber ?? ''),
            'gender_allowed' => $this->genderAllowed,
            'status' => $this->status,
            'unit_type_id' => $this->unitTypeId,
            'unit_cluster_id' => $this->unitClusterId,
        ];

        // If editing, ensure the unit ID is set
        $unit = UnitModel::updateOrCreate(
            ['id' => $this->unitIdBeingEdited],
            $data
        );

        // Handle image uploads
        if (!empty($this->unitImages)) {
            foreach ($this->unitImages as $image) {
                if ($image instanceof TemporaryUploadedFile) {
                    $path = $image->store('images', 'public');

                    $unit->attachments()->create([
                        'name' => $image->getClientOriginalName(),
                        'file_name' => basename($path),
                        'mime_type' => $image->getMimeType(),
                        'path' => $path,
                    ]);
                }
            }
        }

        // Delete marked images if any
        if (!empty($this->imagesToDelete)) {
            foreach ($this->imagesToDelete as $attachmentId) {
                $attachment = $unit->attachments()->find($attachmentId);
                if ($attachment) {
                    Storage::disk('public')->delete($attachment->path);
                    $attachment->delete();
                }
            }
        }

        // Flash message
        LivewireAlert::title($this->unitIdBeingEdited ? 'Data berhasil diperbarui.' : 'Unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Reset form and close modal
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of a unit.
     *
     * @param array $data
     */
    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus data Nomor Kamar '. $data['room_number'] . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!') // Confirm button to delete method
            ->onConfirm('deleteUnit', ['id' => $data['id']])
            ->show();
    }

    /**
     * Delete a unit.
     *
     * @param array $data
     */
    public function deleteUnit($data)
    {
        // Validate the ID
        $id = $data['id'];
        $unit = UnitModel::find($id);

        if ($unit) {
            // Delete all associated images first
            $attachments = $unit->attachments()->get();
            foreach ($attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }

            // Delete the unit
            $roomNumber = $unit->room_number;
            $unit->delete();

            // Flash success message
            LivewireAlert::title('Berhasil Dihapus')
                ->text('Unit ' . $roomNumber . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Reset the form fields.
     */
    private function resetForm()
    {
        $this->roomNumber = '';
        $this->capacity = '';
        $this->virtualAccountNumber = '';
        $this->genderAllowed = '';
        $this->status = '';
        $this->unitTypeId = '';
        $this->unitClusterId = '';
        $this->unitIdBeingEdited = null;
    }

    /**
     * Remove an image from the unit images array.
     *
     * @param int $index
     */
    public function removeUnitImage($index)
    {
        if (isset($this->unitImages[$index])) {
            $image = $this->unitImages[$index];
            if ($image instanceof TemporaryUploadedFile) {
                $image->delete();
            } else {
                Storage::disk('public')->delete($image);
            }
            unset($this->unitImages[$index]);
        }
    }

    /**
     * Mark an image for deletion.
     *
     * @param int $attachmentId
     */
    public function markImageForDeletion($attachmentId)
    {
        if (!in_array($attachmentId, $this->imagesToDelete)) {
            $this->imagesToDelete[] = $attachmentId;
        }

        // Remove the image from existing images array
        $this->existingImages = collect($this->existingImages)->reject(function ($image) use ($attachmentId) {
            return $image->id == $attachmentId;
        });
    }

    /**
     * Export units data to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        // Validate the search and filter parameters
        $units = UnitModel::query()
            ->when($this->search, fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->when($this->genderAllowedFilter, fn($q) => $q->where("gender_allowed", $this->genderAllowedFilter))
            ->when($this->statusFilter, fn($q) => $q->where("status", $this->statusFilter))
            ->when($this->unitTypeFilter, fn($q) => $q->where("unit_type_id", $this->unitTypeFilter))
            ->when($this->unitClusterFilter, fn($q) => $q->where("unit_cluster_id", $this->unitClusterFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->get();

        // Prepare data for PDF export
        $pdfData = $units->map(function ($unit) {
            return [
                'room_number' => $unit->room_number,
                'capacity' => $unit->capacity,
                'virtual_account_number' => (string) $unit->virtual_account_number,
                'gender_allowed' => GenderAllowed::from($unit->gender_allowed)->label(),
                'status' => UnitStatus::from($unit->status)->label(),
                'unit_type_id' => $unit->unitType ? $unit->unitType->name : '',
                'unit_cluster_id' => $unit->unitCluster ? $unit->unitCluster->name : '',
            ];
        });

        // Show processing alert
        LivewireAlert::title('Memproses PDF...')
            ->text('Mohon tunggu.')
            ->info()
            ->toast()
            ->position('top-end')
            ->show();

        // Load the PDF view with the data
        $pdf = Pdf::loadView('exports.units', ['units' => $pdfData]);

        // Return the PDF as a downloadable response
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, now()->format('Y-m-d') . '_units.pdf');
    }

    /**
     * Export units data to Excel.
     *
     */
    public function exportExcel()
    {
        // Show processing alert
        LivewireAlert::title('Memproses Excel...')
            ->text('Mohon tunggu.')
            ->info()
            ->toast()
            ->position('top-end')
            ->show();


        return Excel::download(
            new UnitsExport(
                $this->search,
                $this->genderAllowedFilter,
                $this->statusFilter,
                $this->unitTypeFilter,
                $this->unitClusterFilter,
                $this->orderBy,
                $this->sort
            ),
            now()->format('Y-m-d') . '_units.xlsx'
        );
    }
}
