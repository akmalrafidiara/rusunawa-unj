<?php

namespace App\Livewire\Managers;

use App\Enums\GenderAllowed;
use App\Enums\UnitStatus;
use App\Exports\UnitsExport;
use App\Models\Unit as UnitModel;
use App\Models\UnitCluster;
use App\Models\UnitType;
use App\Models\UnitRate;
use App\Models\Attachment;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
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
        $notes,
        $image,
        $unitTypeId,
        $unitClusterId,
        $createdAt,
        $updatedAt,
        $unitTypeName,
        $unitClusterName;

    // Temporary image for editing
    public $temporaryImage;

    // Rates properties
    public $unitRates = [];
    public $unitRatesId = [];

    // Options properties
    public $genderAllowedOptions;
    public $statusOptions;
    public $unitTypeOptions;
    public $unitClusterOptions;
    public $rateOptions = [];


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

        $this->rateOptions = UnitRate::select('id', 'price', 'occupant_type', 'pricing_basis')
            ->get()
            ->map(fn($rate) => [
                'value' => $rate->id,
                'label' => $rate->occupant_type . ' - Rp ' . number_format($rate->price) . ' (' . ucfirst(str_replace('_', ' ', $rate->pricing_basis->value)) . ')',
            ])->toArray();
        }

    /**
     * Membangun instance query builder untuk unit dengan semua filter dan sorting yang diterapkan.
     * Ini adalah satu-satunya sumber untuk semua query unit di komponen ini.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildUnitQuery()
    {
        return UnitModel::query()
            ->when($this->search, fn($q) => $q->where('room_number', 'like', "%{$this->search}%"))
            ->when($this->genderAllowedFilter, fn($q) => $q->where("gender_allowed", $this->genderAllowedFilter))
            ->when($this->statusFilter, fn($q) => $q->where("status", $this->statusFilter))
            ->when($this->unitTypeFilter, fn($q) => $q->where("unit_type_id", $this->unitTypeFilter))
            ->when($this->unitClusterFilter, fn($q) => $q->where("unit_cluster_id", $this->unitClusterFilter))
            ->with(['unitType', 'unitCluster']) // Pastikan eager loading ada di sini
            ->orderBy($this->orderBy, $this->sort);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $units = $this->buildUnitQuery()->paginate($this->perPage);

        return view('livewire.managers.oprations.units.index', compact('units'));
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
        $this->notes = $unit->notes;
        $this->image = $unit->image;
        $this->unitTypeId = $unit->unit_type_id;
        $this->unitClusterId = $unit->unit_cluster_id;

        // Filling rates
        $this->unitRates = $unit->rates->sortBy('occupant_type')->map(function ($rate) {
            return [
                'id' => $rate->id,
                'price' => $rate->price,
                'occupant_type' => $rate->occupant_type,
                'pricing_basis' => $rate->pricing_basis,
                'requires_verification' => $rate->requires_verification,
            ];
        })->values()->toArray();

        $this->unitRatesId = $unit->rates->pluck('id')->toArray();

        // Filling images data
        $this->temporaryImage = $unit->image;

        // Filling detail
        $this->unitTypeName = $unit->unitType->name;
        $this->unitClusterName = $unit->unitCluster->name;
        $this->createdAt = $unit->created_at;
        $this->updatedAt = $unit->updated_at;
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
            'genderAllowed' => ['required', Rule::in(GenderAllowed::values())],
            'status' => ['required', Rule::in(UnitStatus::values())],
            'unitTypeId' => 'required|exists:unit_types,id',
            'unitClusterId' => 'required|exists:unit_clusters,id',
            // --- Consolidated Validation Rule ---
            'image' => $this->unitIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            'unitRatesId.*' => [
                'nullable',
                'exists:rates,id',
            ],
        ];
    }


    /**
     * Validate the uploaded file.
     *
     * @return bool
     */
    public function validateUploadedFile()
    {
        $this->validate([
            'image' => $this->rules()['image'],
        ]);

        return true;
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
            'notes' => $this->notes,
            'unit_type_id' => $this->unitTypeId,
            'unit_cluster_id' => $this->unitClusterId,
        ];

        // Jika tidak ada gambar lama di hapus
        if ($this->image !== $this->temporaryImage && $this->temporaryImage != null) {
            Storage::disk('public')->delete($this->temporaryImage);
            $data['image'] = null;
        }

        // Jika ada gambar baru yang diupload
        if ($this->image instanceof TemporaryUploadedFile) {

            // Hapus gambar lama jika sedang edit
            if ($this->unitIdBeingEdited && $this->temporaryImage != null) {
                if ($this->image !== $this->temporaryImage) {
                    Storage::disk('public')->delete($this->temporaryImage);
                }
            }

            // Simpan gambar baru ke storage
            $data['image'] = $this->image->store('images', 'public');
        }

        // If editing, ensure the unit ID is set
        $unit = UnitModel::updateOrCreate(
            ['id' => $this->unitIdBeingEdited],
            $data
        );

        // Unit Rates
        if (!empty($this->unitRatesId)) {
            $unit->rates()->sync($this->unitRatesId);
        } else {
            $unit->rates()->detach();
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
            // Hapus gambar dari storage jika ada
            if ($unit->image) {
                Storage::disk('public')->delete($unit->image);
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
        $this->notes = '';
        $this->image = null;
        $this->unitTypeId = '';
        $this->unitClusterId = '';
        $this->unitIdBeingEdited = null;
        
        $this->temporaryImage = null;
        
        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Export units data to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        // Validate the search and filter parameters
        $units = $this->buildUnitQuery()->get();

        // Prepare data for PDF export
        $pdfData = $units->map(function ($unit) {
            return [
                'room_number' => $unit->room_number,
                'capacity' => $unit->capacity,
                'virtual_account_number' => (string) $unit->virtual_account_number,
                'gender_allowed' => $unit->gender_allowed->label(),
                'status' => $unit->status->label(),
                'unit_type_id' => $unit->unitType ? $unit->unitType->name : '',
                'unit_cluster_id' => $unit->unitCluster ? $unit->unitCluster->name : '',
            ];
        });

        // Show processing alert
        LivewireAlert::title('PDF Berhasil Diunduh')
            ->text('Data unit berhasil diekspor ke PDF.')
            ->success()
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
        $units = $this->buildUnitQuery()->get();

        LivewireAlert::title('PDF Berhasil Diunduh')
            ->text('Data unit berhasil diekspor ke PDF.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        return Excel::download(
            new UnitsExport($units),
            now()->format('Y-m-d') . '_units.xlsx'
        );
    }
}
