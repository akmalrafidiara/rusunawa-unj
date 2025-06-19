<?php

namespace App\Livewire\Managers;

use App\Models\UnitType as UnitTypeModel;
use App\Models\Attachment;
use App\Models\UnitRate;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class UnitType extends Component
{
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public 
        $name, 
        $description, 
        $createdAt, 
        $updatedAt;
        
    public $facilities = []; // Pastikan ini selalu array
    public $newFacility = '';

    public $unitTypeData;

    // Attachment properties
    public $attachments = []; // Untuk upload baru
    public $existingAttachments = []; // Untuk tampilkan yang sudah ada
    public $attachmentsToDelete = []; // Untuk hapus attachment

    // Toolbar properties
    public $search = '';
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $unitTypeIdBeingEdited = null;
    public $modalType = '';
    public $detailedUnitType;

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    protected $listeners = [
        'closeModal' => 'resetForm',
    ];

    public function mount()
    {
        // 
    }

    public function render()
    {
        $unitTypes = UnitTypeModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        $unitTypes->getCollection()->transform(function ($unitType) {
            $unitType->facilities = $unitType->facilities ?? [];
            $unitType->attachments = $unitType->attachments()->get();
            return $unitType;
        });

        return view('livewire.managers.oprations.unit-types.index', compact('unitTypes'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function edit(UnitTypeModel $unitType)
    {
        $this->fillData($unitType);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function detail(UnitTypeModel $unitType)
    {
        $this->fillData($unitType);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    public function price(UnitTypeModel $unitType)
    {
        $this->unitTypeData = $unitType;
        $this->modalType = 'price';
        $this->showModal = true;
    }

    protected function fillData(UnitTypeModel $unitType)
    {
        $this->unitTypeIdBeingEdited = $unitType->id;
        $this->name = $unitType->name;
        $this->description = $unitType->description;
        $this->facilities = $unitType->facilities ?? [];
        $this->existingAttachments = $unitType->attachments()->get();
        $this->attachments = [];
        $this->attachmentsToDelete = [];
        $this->createdAt = $unitType->created_at;
        $this->updatedAt = $unitType->updated_at;
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'facilities' => 'array',
            'facilities.*' => 'string|max:255',
            'attachments.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'facilities' => $this->facilities, // Laravel akan otomatis meng-encode jika di-cast di model
        ];

        $unitType = UnitTypeModel::updateOrCreate(
            ['id' => $this->unitTypeIdBeingEdited],
            $data
        );

        // Hapus attachment yang dihapus user
        $this->handleAttachmentDeletions($unitType);

        // Upload attachment baru
        $this->handleAttachmentUploads($unitType);

        LivewireAlert::title($this->unitTypeIdBeingEdited ? 'Data berhasil diperbarui.' : 'Tipe unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

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
            // Hapus semua attachment
            foreach ($unitType->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }
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
        $this->facilities = [];
        $this->attachments = [];
        $this->existingAttachments = [];
        $this->attachmentsToDelete = [];
        $this->unitTypeIdBeingEdited = null;
        $this->showModal = false;
        $this->modalType = ''; // Reset modal type
        $this->detailedUnitType = null; // Reset detailedUnitType
        $this->resetErrorBag();
        $this->resetValidation();
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
        $this->facilities = array_values($this->facilities);
    }

    // === Attachment Handling ===

    public function updatedAttachments()
    {
        $this->resetErrorBag('attachments.*');
        $this->validateOnly('attachments.*');
    }

    private function handleAttachmentUploads(UnitTypeModel $unitType)
    {
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $path = $file->store('attachments/unit-types', 'public');
                $unitType->attachments()->create([
                    'name' => $file->getClientOriginalName(),
                    'file_name' => basename($path),
                    'mime_type' => $file->getMimeType(),
                    'path' => $path,
                ]);
            }
        }
    }

    private function handleAttachmentDeletions(UnitTypeModel $unitType)
    {
        if (!empty($this->attachmentsToDelete)) {
            $attachments = Attachment::whereIn('id', $this->attachmentsToDelete)->get();
            foreach ($attachments as $attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            }
        }
    }

    public function queueAttachmentForDeletion($attachmentId)
    {
        if (!in_array($attachmentId, $this->attachmentsToDelete)) {
            $this->attachmentsToDelete[] = $attachmentId;
        }
        $this->existingAttachments = collect($this->existingAttachments)->reject(function ($attachment) use ($attachmentId) {
            return $attachment['id'] == $attachmentId;
        })->values();
    }
}
