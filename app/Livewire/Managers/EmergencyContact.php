<?php

namespace App\Livewire\Managers; // Sesuaikan namespace jika diperlukan

use App\Enums\EmergencyContactRole;
use App\Models\Contact as ContactModel; // Menggunakan alias untuk menghindari konflik nama
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class EmergencyContact extends Component
{
    // Traits
    use WithPagination;

    // Main data properties
    public
        $name,
        $role,
        $phone,
        $address,
        $createdAt,
        $updatedAt;

    // Options properties
    public $roleOptions;

    // Filter properties
    public $search = '';
    public $roleFilter = '';

    // Pagination and sorting properties
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    // Modal properties
    public $showModal = false;
    public $modalType = ''; // 'form' or 'detail'
    public $contactIdBeingEdited = null;

    // Query string properties
    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    /**
     * Initialize the component.
     */
    public function mount()
    {
        // Muat opsi peran dari enum EmergencyContactRole
        $this->roleOptions = EmergencyContactRole::options();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $contacts = ContactModel::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->where("role", $this->roleFilter))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage); // Menggunakan $this->perPage

        return view('livewire.managers.contents.emergency-contacts.index', compact('contacts'));
    }

    /**
     * Open modal for creating a new contact.
     */
    public function create()
    {
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Open modal for editing or viewing details of a contact.
     *
     * @param ContactModel $contact
     */
    public function edit(ContactModel $contact)
    {
        $this->fillData($contact);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    /**
     * Open modal for viewing details of a contact.
     *
     * @param ContactModel $contact
     */
    public function detail(ContactModel $contact)
    {
        $this->fillData(contact: $contact);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * Fill data for edit or detail modal.
     *
     * @param ContactModel $contact
     */
    protected function fillData(ContactModel $contact)
    {
        $this->contactIdBeingEdited = $contact->id;
        $this->name = $contact->name;
        $this->role = $contact->role->value; // Mengambil nilai string dari enum
        $this->phone = $contact->phone;
        $this->address = $contact->address;

        // Filling detail
        $this->createdAt = $contact->created_at;
        $this->updatedAt = $contact->updated_at;
    }

    /**
     * Rules for validation.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'role' => ['required', Rule::in(EmergencyContactRole::values())],
            'phone' => 'required|string|max:20', // Sesuaikan aturan validasi telepon
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Validate the form when a property is updated.
     *
     * @param string $propertyName
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    /**
     * Save the contact data.
     */
    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'name' => $this->name,
            'role' => $this->role,
            'phone' => $this->phone,
            'address' => $this->address,
        ];

        ContactModel::updateOrCreate(
            ['id' => $this->contactIdBeingEdited],
            $data
        );

        LivewireAlert::title($this->contactIdBeingEdited ? 'Data kontak berhasil diperbarui.' : 'Kontak darurat berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    /**
     * Confirm deletion of a contact.
     *
     * @param array $data
     */
    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus kontak "' . $data['name'] . '"?')
            ->text('Apakah Anda yakin ingin menghapus kontak darurat ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteContact', ['id' => $data['id']])
            ->show();
    }

    /**
     * Delete a contact.
     *
     * @param array $data
     */
    public function deleteContact($data)
    {
        $id = $data['id'];
        $contact = ContactModel::find($id);

        if ($contact) {
            $name = $contact->name;
            $contact->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Kontak "' . $name . '" telah dihapus.')
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
        $this->name = '';
        $this->role = '';
        $this->phone = '';
        $this->address = '';
        $this->contactIdBeingEdited = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}