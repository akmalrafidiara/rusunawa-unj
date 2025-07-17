<?php

namespace App\Livewire\Managers;

use App\Enums\OccupantStatus;
use App\Models\Occupant as OccupantModel;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\LivewireFilepond\WithFilePond;

class Occupant extends Component
{
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public
        $fullName,
        $email,
        $whatsappNumber,
        $identityCardFile,
        $communityCardFile,
        $agreeToRegulation,
        $notes,
        $status;

    public bool $isStudent = false;

    public
        $studentId,
        $faculty,
        $studyProgram,
        $classYear;

    // Temporary file properties for uploads
    public
        $temporaryIdentityCardFile,
        $temporaryCommunityCardFile;

    // Options
    public $statusOptions;

    // Filter properties
    public $search = '';
    public $statusFilter = '';

    // Pagination and sorting properties
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'asc';

    // Modal properties
    public $showModal = false;
    public $modalType = '';
    public $occupantIdBeingEdited = null;

    // Query sting properties
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->statusOptions = OccupantStatus::options();
    }

    private function buildUnitQuery()
    {
        return OccupantModel::query()
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('whatsapp_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->orderBy, $this->sort);
    }

    // Metode untuk merender tampilan
    public function render()
    {
        $occupants = $this->buildUnitQuery()->paginate($this->perPage);

        return view('livewire.managers.tenancy.occupants.index', compact('occupants'));
    }

    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(OccupantModel $occupant)
    {
        $this->fillData($occupant);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function detail(OccupantModel $occupant)
    {
        $this->fillData($occupant);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    public function fillData(OccupantModel $occupant)
    {
        $this->occupantIdBeingEdited = $occupant->id;
        $this->fullName = $occupant->full_name;
        $this->email = $occupant->email;
        $this->whatsappNumber = $occupant->whatsapp_number;
        $this->identityCardFile = $occupant->identity_card_file;
        $this->communityCardFile = $occupant->community_card_file;
        $this->agreeToRegulation = $occupant->agree_to_regulation;
        $this->notes = $occupant->notes;
        $this->status = $occupant->status;

        if ($occupant->is_student) {
            $this->isStudent = true;
            $this->studentId = $occupant->student_id;
            $this->faculty = $occupant->faculty;
            $this->studyProgram = $occupant->study_program;
            $this->classYear = $occupant->class_year;
        } else {
            $this->isStudent = false;
            $this->studentId = null;
            $this->faculty = null;
            $this->studyProgram = null;
            $this->classYear = null;
        }
    }

    public function rules()
    {
        $rules = [
            'fullName' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsappNumber' => 'nullable|string|max:20',
            'identityCardFile' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'communityCardFile' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'agreeToRegulation' => 'accepted',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:' . implode(',', OccupantStatus::values()),
        ];

        if ($this->isStudent) {
            $rules['studentId'] = 'required|string|max:20';
            $rules['faculty'] = 'required|string|max:100';
            $rules['studyProgram'] = 'required|string|max:100';
            $rules['classYear'] = 'required|integer|min:1900|max:' . date('Y');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'fullName.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'whatsappNumber.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 karakter.',
            'identityCardFile.file' => 'File KTP harus berupa file.',
            'communityCardFile.file' => 'File Kartu Komunitas harus berupa file.',
            'agreeToRegulation.accepted' => 'Anda harus menyetujui peraturan.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    public function validateUploadedFile(): bool
    {
        $this->validate([
            'identityCardFile' => $this->rules()['identityCardFile'],
            'communityCardFile' => $this->rules()['communityCardFile'],
        ]);

        return true;
    }

    public function updated($propertyName)
    {
        // Validate only the changed property if it exists in the rules
        if (in_array($propertyName, array_keys($this->rules()))) {
            $this->validateOnly($propertyName, $this->rules());
        }
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsappNumber,
            'agree_to_regulation' => $this->agreeToRegulation,
            'notes' => $this->notes,
            'status' => $this->status,
            
            'is_student'           => $this->isStudent,
            'student_id'           => $this->isStudent ? $this->studentId : null,
            'faculty'              => $this->isStudent ? $this->faculty : null,
            'study_program'        => $this->isStudent ? $this->studyProgram : null,
            'class_year'           => $this->isStudent ? $this->classYear : null,
        ];

        if ($this->identityCardFile !== $this->temporaryidentityCardFile && $this->temporaryidentityCardFile != null) {
            Storage::disk('public')->delete($this->temporaryidentityCardFile);
            $data['identity_card_file'] = null;
        }

        if ($this->communityCardFile !== $this->temporarycommunityCardFile && $this->temporarycommunityCardFile != null) {
            Storage::disk('public')->delete($this->temporarycommunityCardFile);
            $data['community_card_file'] = null;
        }

        if ($this->identityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingEdited && $this->temporaryidentityCardFile != null) {
                if ($this->identityCardFile !== $this->temporaryidentityCardFile) {
                    Storage::disk('public')->delete($this->temporaryidentityCardFile);
                }
            }
            
            $data['identity_card_file'] = $this->identityCardFile->store('occupant', 'public');
        }

        if ($this->communityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingEdited && $this->temporarycommunityCardFile != null) {
                if ($this->communityCardFile !== $this->temporarycommunityCardFile) {
                    Storage::disk('public')->delete($this->temporarycommunityCardFile);
                }
            }
            
            $data['community_card_file'] = $this->communityCardFile->store('occupant', 'public');
        }

        Occupant::updateOrCreate(['id' => $this->occupantIdBeingEdited], $data);

        LivewireAlert::title($this->occupantIdBeingEdited ? 'Data berhasil diperbarui.' : 'Unit berhasil ditambahkan.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    public function resetForm()
    {
        $this->reset([
            'fullName',
            'email',
            'whatsappNumber',
            'identityCardFile',
            'communityCardFile',
            'agreeToRegulation',
            'notes',
            'status',
            'isStudent',
            'studentId',
            'faculty',
            'studyProgram',
            'classYear',
            'temporaryIdentityCardFile',
            'temporaryCommunityCardFile',
        ]);

        $this->occupantIdBeingEdited = null;
        $this->showModal = false;
    }
}
