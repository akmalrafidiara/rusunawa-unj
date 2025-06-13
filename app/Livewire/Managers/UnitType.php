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
    public $name, $description, $image, $temporaryImage;
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

        return view('livewire.managers.oprations.unit-types.index', compact('unitTypes'));
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
        $this->temporaryImage = $unitType->image;
        $this->facilities = json_decode($unitType->facilities, true) ?? [];
        $this->showModal = true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => $this->unitTypeIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facilities' => 'array',
            'facilities.*' => 'string|max:255',
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
            'description' => $this->description,
            'facilities' => json_encode($this->facilities),
        ];

        // Jika tidak ada gambar lama di hapus
        if ($this->image !== $this->temporaryImage && $this->temporaryImage != null) {
            Storage::disk('public')->delete($this->temporaryImage);
            $data['image'] = null;
        }


        // Jika ada gambar baru yang diupload
        if ($this->image instanceof TemporaryUploadedFile) {

            // Hapus gambar lama jika sedang edit
            if ($this->unitTypeIdBeingEdited && $this->temporaryImage != null) {
                if ($this->image !== $this->temporaryImage) {
                    Storage::disk('public')->delete($this->temporaryImage);
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

        // Flash message
        LivewireAlert::title($this->unitTypeIdBeingEdited ? 'Data berhasil diperbarui.' : 'Tipe unit berhasil ditambahkan.')
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
            ->onConfirm('deleteUnitType', ['id' => $data['id']])
            ->show();
    }

    public function deleteUnitType($data)
    {
        $id = $data['id'];
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
        $this->temporaryImage = '';
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
