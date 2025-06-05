<?php

namespace App\Livewire\Managers;

use App\Models\UnitType as UnitTypeModel;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class UnitType extends Component
{
    use WithFileUploads;

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
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'facilities' => 'array',
            'facilities.*' => 'string|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'facilities' => json_encode($this->facilities),
        ];

        if ($this->image) {
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
        $save = UnitTypeModel::updateOrCreate(
            ['id' => $this->unitTypeIdBeingEdited],
            $data
        );

        // Reset form
        $this->resetForm();
        $this->showModal = false;

        // Flash message
        session()->flash('message', $this->unitTypeIdBeingEdited ? 'Tipe unit berhasil diperbaharui.' : 'Tipe unit berhasil ditambahkan.');
        $this->dispatch('swal:success', title: 'Berhasil!');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
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

            session()->flash('message', 'Tipe unit berhasil dihapus.');
            $this->dispatch('swal:success', title: 'Berhasil!');
        } else {
            session()->flash('error', 'Tipe unit tidak ditemukan.');
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
