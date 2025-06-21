<?php

namespace App\Livewire\Managers;

use App\Models\OccupantType as OccupantTypeModel;
use App\Models\UnitCluster;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class OccupantType extends Component
{
    // Main data properties
    public $name, $description, $requiresVerification;

    public $accessibleClusters = [];

    // Toolbar properties
    public $search = '';
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $unitRateIdBeingEdited = null;

    // Options Properties
    public $unitClusterOptions = [];

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
        $this->unitClusterOptions = UnitCluster::all()->pluck('name', 'id')->toArray();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $occupantTypes = OccupantTypeModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->get();
        return view('livewire.managers.oprations.occupant-types.index', compact('occupantTypes'));
    }

    /**
     * Create a new occupant type.
     */
    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }


    /**
     * Edit an existing unit rate.
     *
     * @param OccupantTypeModel $occupantType
     */
    public function edit(OccupantTypeModel $occupantType)
    {
        $this->unitRateIdBeingEdited = $occupantType->id;
        $this->name = $occupantType->name;
        $this->description = $occupantType->description;
        $this->requiresVerification = $occupantType->requires_verification;

        $this->accessibleClusters = $occupantType->accessibleClusters->pluck('id')->toArray();

        $this->showModal = true;
    }

    /**
     * Validation rules for the form.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'requiresVerification' => 'boolean',
        ];
    }

    /**
     * Save the unit rate data.
     */
    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'requires_verification' => $this->requiresVerification,
        ];

        $occupantType = OccupantTypeModel::updateOrCreate(
            ['id' => $this->unitRateIdBeingEdited],
            $data
        );

        $occupantType->accessibleClusters()->sync($this->accessibleClusters);

        // Flash message
        LivewireAlert::title($this->unitRateIdBeingEdited ? 'Data berhasil diperbarui.' : 'Rate unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Reset form
        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of an occupant type.
     *
     * @param array $data
     */
    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus data harga tipe penghuni ' . $data['name'] . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteOccupantType', ['id' => $data['id']])
            ->show();
    }

    /**
     * Delete an occupant type.
     *
     * @param array $data
     */
    public function deleteOccupantType($data)
    {
        $id = $data['id'];
        $occupantType = OccupantTypeModel::find($id);
        if ($occupantType) {
            $occupantType->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($occupantType->name . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Refresh the occupant type data properties.
     */
    public function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->requiresVerification = false;
        $this->unitRateIdBeingEdited = null;
        $this->accessibleClusters = [];
    }
}
