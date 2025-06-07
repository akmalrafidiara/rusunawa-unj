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
    use WithFileUploads;
    use WithFilePond;

    public $search = '';
    public $name, $address, $image, $description, $staffId, $staffName, $temporaryImage, $createdAt;

    public $staffOptions;

    public $orderBy = 'created_at';
    public $sort = 'asc';

    public $showModal = false;
    public $modalType;
    public $unitClusterIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->staffOptions = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'staff_of_rusunawa');
        })->get()->map(function ($user) {
            return [
                'value' => $user->id,
                'label' => $user->name,
            ];
        })->toArray();
    }

    public function render()
    {
        $unitClusters = UnitClusterModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        return view('livewire.managers.unit-cluster', compact('unitClusters'));
    }

    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    // Refactor: Use a single method to fill form fields from a UnitClusterModel
    protected function fillData(UnitClusterModel $unitCluster)
    {
        $this->unitClusterIdBeingEdited = $unitCluster->id;
        $this->name = $unitCluster->name;
        $this->address = $unitCluster->address;
        $this->description = $unitCluster->description;
        $this->image = $unitCluster->image;
        $this->staffId = $unitCluster->staff_id;
        $this->staffName = $unitCluster->staff_name;
        $this->temporaryImage = $unitCluster->image;
        $this->createdAt = $unitCluster->created_at;
    }

    public function edit(UnitClusterModel $unitCluster)
    {
        $this->fillData($unitCluster);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function detail(UnitClusterModel $unitCluster)
    {
        $this->fillData($unitCluster);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'staffId' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::where('id', $value)
                        ->whereHas('roles', function ($q) {
                            $q->where('name', 'staff_of_rusunawa');
                        })->first();
                    if (!$user) {
                        $fail('The selected staff is invalid or does not have the staff role.');
                    }
                }
            ],
            'description' => 'nullable|string',
            'image' => $this->unitClusterIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function validateUploadedFile()
    {
        $this->validate([
            'image' => $this->rules()['image'],
        ]);

        return true;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'name' => $this->name,
            'address'=> $this->address,
            'description' => $this->description,
            'staff_id' => $this->staffId,
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

    public function resetForm() {
        $this->name = '';
        $this->address = '';
        $this->description = '';
        $this->image = '';
        $this->staffId = '';
        $this->temporaryImage = '';
        $this->unitClusterIdBeingEdited = null;
    }
}
