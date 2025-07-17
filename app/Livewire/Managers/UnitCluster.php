<?php

namespace App\Livewire\Managers;

use App\Models\UnitCluster as UnitClusterModel;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Spatie\LivewireFilepond\WithFilePond;

class UnitCluster extends Component
{
    // Traits
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public $name, $address, $image, $description, $createdAt, $updatedAt; // Removed staffId, staffName

    // Temporary image for editing
    public $temporaryImage;

    // Options properties
    // public $staffOptions; // Removed staffOptions

    // Toolbar properties
    public $search = '';
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $modalType;
    public $unitClusterIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        // Removed staffOptions population
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $unitClusters = UnitClusterModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        return view('livewire.managers.oprations.unit-clusters.index', compact('unitClusters'));
    }

    /**
     * Reset the component state.
     */
    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Fill the form data with the given unit cluster model.
     *
     * @param UnitClusterModel $unitCluster
     */
    protected function fillData(UnitClusterModel $unitCluster)
    {
        $this->unitClusterIdBeingEdited = $unitCluster->id;
        $this->name = $unitCluster->name;
        $this->address = $unitCluster->address;
        $this->description = $unitCluster->description;
        $this->image = $unitCluster->image;
        // Removed staffId and staffName filling
        $this->temporaryImage = $unitCluster->image;
        $this->createdAt = $unitCluster->created_at;
        $this->updatedAt = $unitCluster->updated_at;
    }

    /**
     * Show the form to edit or view the unit cluster.
     *
     * @param UnitClusterModel $unitCluster
     */
    public function edit(UnitClusterModel $unitCluster)
    {
        $this->fillData($unitCluster);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    /**
     * Show the detail view of the unit cluster.
     *
     * @param UnitClusterModel $unitCluster
     */
    public function detail(UnitClusterModel $unitCluster)
    {
        $this->fillData($unitCluster);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * Define the validation rules for the form.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            // Removed staffId validation
            'description' => 'nullable|string',
            'image' => $this->unitClusterIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
     * Validate the form data when a property is updated.
     *
     * @param string $propertyName
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    /**
     * Save the unit cluster data.
     */
    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'name' => $this->name,
            'address'=> $this->address,
            'description' => $this->description,
            // 'staff_id' => $this->staffId, // Removed staff_id
        ];

        // Jika tidak ada gambar lama di hapus
        if ($this->image !== $this->temporaryImage && $this->temporaryImage != null) {
            Storage::disk('public')->delete($this->temporaryImage);
            $data['image'] = null;
        }


        // Jika ada gambar baru yang diupload
        if ($this->image instanceof TemporaryUploadedFile) {

            // Hapus gambar lama jika sedang edit
            if ($this->unitClusterIdBeingEdited && $this->temporaryImage != null) {
                if ($this->image !== $this->temporaryImage) {
                    Storage::disk('public')->delete($this->temporaryImage);
                }
            }

            // Simpan gambar baru ke storage
            $data['image'] = $this->image->store('images', 'public');
        }

        // Simpan atau update data
        UnitClusterModel::updateOrCreate(
            ['id' => $this->unitClusterIdBeingEdited],
            $data
        );

        // Flash message
        LivewireAlert::title($this->unitClusterIdBeingEdited ? 'Data berhasil diperbarui.' : 'Tipe unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Reset form
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of the unit cluster.
     *
     * @param array $data
     */
    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus data '. $data['name'] . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteUnitCluster', ['id' => $data['id']])
            ->show();
    }

    /**
     * Delete the unit cluster.
     *
     * @param array $data
     */
    public function deleteUnitCluster($data)
    {
        $id = $data['id'];
        $unitCluster = UnitClusterModel::find($id);
        if ($unitCluster) {
            // Hapus gambar dari storage jika ada
            if ($unitCluster->image) {
                Storage::disk('public')->delete($unitCluster->image);
            }

            // Hapus unit type
            $unitCluster->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($unitCluster->name . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Reset the form fields.
     */
    public function resetForm() {
        $this->name = '';
        $this->address = '';
        $this->description = '';
        $this->image = '';
        // $this->staffId = ''; // Removed staffId
        $this->temporaryImage = '';
        $this->unitClusterIdBeingEdited = null;
    }
}