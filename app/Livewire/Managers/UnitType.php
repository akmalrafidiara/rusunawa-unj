<?php

namespace App\Livewire\Managers;

use App\Models\UnitType as UnitTypeModel;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Spatie\LivewireFilepond\WithFilePond;

class UnitType extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $search = '';
    public $name, $description, $image;
    public $facilities = [];
    public $newFacility = '';

    public $orderBy = 'created_at';
    public $sort = 'asc';

    public $showModal = false;
    public $unitTypeIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function render()
    {
        $unitTypes = UnitTypeModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        $unitTypes->getCollection()->transform(function ($unitType) {
            $unitType->facilities = json_decode($unitType->facilities, true);
            return $unitType;
        });

        return view('livewire.managers.unit-type', compact('unitTypes'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(UnitTypeModel $unitType)
    {
        $this->unitTypeIdBeingEdited = $unitType->id;
        $this->name = $unitType->name;
        $this->description = $unitType->description;
        $this->image = $unitType->image;
        $this->facilities = json_decode($unitType->facilities, true) ?? [];
        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'string',
            'facilities' => 'array',
            'facilities.*' => 'string|max:255',
        ];

        if ($this->image instanceof TemporaryUploadedFile) {
            $rules['image'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'facilities' => json_encode($this->facilities),
        ];

        if ($this->image instanceof TemporaryUploadedFile) {
            // Hapus gambar lama jika sedang edit
            if ($this->unitTypeIdBeingEdited) {
                $oldType = UnitTypeModel::find($this->unitTypeIdBeingEdited);
                if ($oldType && $oldType->image) {
                    Storage::disk('public')->delete($oldType->image);
                }
            }

            // Simpan gambar baru ke storage
            $data['image'] = $this->image->store('images', 'public');
        }

        // Simpan atau update data
        UnitTypeModel::updateOrCreate(
            ['id' => $this->unitTypeIdBeingEdited],
            $data
        );

        // Reset form
        $this->resetForm();
        $this->showModal = false;

        // Flash message
        LivewireAlert::title($this->userIdBeingEdited ? 'Data berhasil diperbarui.' : 'Pengguna berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();
    }

    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus data '. $data['name'] . '?')
            ->text('Apakah Anda yakin ingin menghapus data ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteUnitType', ['id' => $data['id']])
            ->show();
    }

    public function deleteUnitType($id)
    {
        $unitType = UnitTypeModel::find($id);

        if ($unitType) {
            // Hapus gambar dari storage jika ada
            if ($unitType->image) {
                Storage::disk('public')->delete($unitType->image);
            }

            // Hapus unit type
            $unitType->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text($unitType->name . ' telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function resetForm() {
        $this->name = '';
        $this->description = '';
        $this->image = '';
        $this->facilities = [];
        $this->unitTypeIdBeingEdited = null;
    }

    public function addFacility()
    {
        if (!empty($this->newFacility)) {
            $this->facilities[] = $this->newFacility;
            $this->newFacility = '';
        }
    }

    public function removeFacility($index)
    {
        unset($this->facilities[$index]);
        $this->facilities = array_values($this->facilities); // Re-index array
    }
}
