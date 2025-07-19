<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Data\AcademicData;
use App\Enums\GenderAllowed;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\Payment;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class Contract extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $invoice;
    public $amount_paid;
    public $proofOfPayment;
    public $notes;

    // Tambahkan properti untuk kontrak, unit, dan pembayaran jika belum ada
    public $contract;
    public $occupant; // Refers to the currently logged-in occupant (PIC)
    public $unit;
    public $latestInvoice;
    public $invoices;
    public $payments;

    // Occupant Form Properties
    public $occupantIdBeingSelected; // ID of the occupant currently being edited (can be null for new)

    public
        $fullName,
        $email,
        $whatsappNumber,
        $gender,
        $identityCardFile,
        $communityCardFile;

    public $existingIdentityCardFile; // Stores the path to the current identity card file
    public $existingCommunityCardFile; // Stores the path to the current community card file

    public bool $isStudent = false;
    public $studentId, $faculty, $studyProgram, $classYear;

    public $genderOptions = [];
    public $facultyOptions = [];
    public $studyProgramOptions = [];
    public $classYearOptions = [];

    public bool $showModal = false;
    public string $modalType = '';

    public function mount(?Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        $occupantId = Auth::guard('occupant')->user()->id;
        $this->occupant = Occupant::find($occupantId); // This is the logged-in occupant (PIC)

        if ($this->occupant) {
            $this->contract = $this->occupant->contracts()->with('unit', 'invoices', 'payments', 'occupants', 'pic')->first();

            if ($this->contract) {
                $this->unit = $this->contract->unit ?? new Unit();
                $this->latestInvoice = $this->contract->invoices()->latest()->first();
                $this->invoices = $this->contract->invoices()->get();
                $this->payments = $this->contract->payments()->get();
            } else {
                // Jika tidak ada kontrak, inisialisasi properti terkait dengan nilai default atau kosong
                $this->unit = new Unit(); // Inisialisasi unit kosong
                $this->latestInvoice = null;
                $this->invoices = collect(); // Koleksi kosong
                $this->payments = collect(); // Koleksi kosong
            }
        } else {
            $this->contract = null;
            $this->unit = new Unit();
            $this->latestInvoice = null;
            $this->invoices = collect();
            $this->payments = collect();
        }

        $this->genderOptions = GenderAllowed::optionsWithoutGeneral();
        $this->facultyOptions = collect(AcademicData::getFacultiesAndPrograms())->keys()->map(fn($f) => ['value' => $f, 'label' => $f])->toArray();
        $this->classYearOptions = collect(range(date('Y'), date('Y') - 7))->map(fn($y) => ['value' => $y, 'label' => (string)$y])->toArray();

        // Populate study program options initially if a faculty is already selected
        if ($this->faculty) {
            $this->updatedFaculty($this->faculty);
        }
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.contract');
    }

    protected function rules()
    {
        $rules = [
            // Payment Proof Validation
            'amount_paid' => 'required|numeric|min:0',
            'proofOfPayment' => 'required|image|max:2048',
            'notes' => 'nullable|string|max:255',

            // Occupant Validation
            'fullName' => 'required|string|max:255',
            // Email should be unique for new occupants, but not for the current occupant being edited
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('occupants', 'email')->ignore($this->occupantIdBeingSelected)
            ],
            'whatsappNumber' => 'nullable|string|max:20',
            'gender' => [
                'required',
                Rule::in(GenderAllowed::values()),
            ],

            // File rules adjusted for update/create scenarios
            'identityCardFile' => [
                $this->occupantIdBeingSelected && ($this->identityCardFile === $this->existingIdentityCardFile) ? 'nullable' : 'required',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:2048'
            ],
            'communityCardFile' => [
                $this->occupantIdBeingSelected && ($this->communityCardFile === $this->existingCommunityCardFile) ? 'nullable' : 'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:2048'
            ],
        ];

        if ($this->isStudent) {
            $rules['studentId'] = 'required|string|max:20';
            $rules['faculty'] = 'required|string|max:100';
            $rules['studyProgram'] = 'required|string|max:100';
            $rules['classYear'] = 'required|integer|min:1900|max:' . date('Y');
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            // Payment Proof Validation
            'amount_paid.required' => 'Jumlah yang dibayar wajib diisi.',
            'proofOfPayment.required' => 'Bukti pembayaran harus diunggah.',
            'proofOfPayment.image' => 'File yang diunggah harus berupa gambar.',
            'proofOfPayment.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'notes.max' => 'Catatan tidak boleh lebih dari 255 karakter.',

            // Occupant Validation
            'fullName.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'whatsappNumber.required' => 'Nomor WhatsApp wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'identityCardFile.required' => 'Kartu identitas wajib di-upload.',
            'identityCardFile.mimes' => 'File KTP harus .jpg, .jpeg, .png, atau .pdf.',
            'identityCardFile.max' => 'Ukuran file KTP tidak boleh lebih dari 2MB.',
            'communityCardFile.mimes' => 'File Kartu Komunitas/KK harus .jpg, .jpeg, .png, atau .pdf.',
            'communityCardFile.max' => 'Ukuran file Kartu Komunitas/KK tidak boleh lebih dari 2MB.',
            'studentId.required_if' => 'NIM/ID Mahasiswa wajib diisi jika Anda mahasiswa.',
            'faculty.required_if' => 'Fakultas wajib dipilih jika Anda mahasiswa.',
            'studyProgram.required_if' => 'Program Studi wajib dipilih jika Anda mahasiswa.',
            'classYear.required_if' => 'Tahun Angkatan wajib diisi jika Anda mahasiswa.',
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

    public function showHistory()
    {
        $this->showModal = true;
        $this->modalType = 'history';
    }

    public function showPaymentForm()
    {
        $this->showModal = true;
        $this->modalType = 'payment';
    }

    public function showOccupantForm($occupantId = null)
    {
        $this->showModal = true;
        $this->modalType = 'occupant';
        
        $this->reset(['fullName', 'email', 'whatsappNumber', 'gender', 'identityCardFile', 'communityCardFile', 'isStudent', 'studentId', 'faculty', 'studyProgram', 'classYear']);
        $this->resetValidation(); // Clear validation errors when opening form

        if($occupantId){
            $this->occupantIdBeingSelected = $occupantId;
    
            $occupantToEdit = Occupant::find($occupantId);
    
            if ($occupantToEdit) {
                $this->fullName = $occupantToEdit->full_name;
                $this->email = $occupantToEdit->email;
                $this->whatsappNumber = $occupantToEdit->whatsapp_number;
                $this->gender = $occupantToEdit->gender->value;
                $this->identityCardFile = $occupantToEdit->identity_card_file;
                $this->communityCardFile = $occupantToEdit->community_card_file;
                $this->isStudent = $occupantToEdit->is_student;
                $this->studentId = $occupantToEdit->student_id;
                $this->faculty = $occupantToEdit->faculty;
                
                // Ensure study program options are loaded if faculty is set
                if ($this->faculty) {
                    $this->studyProgramOptions = collect(AcademicData::getFacultiesAndPrograms()[$this->faculty] ?? [])->map(fn($sp) => ['value' => $sp, 'label' => $sp])->toArray();
                } else {
                    $this->studyProgramOptions = [];
                }
                $this->studyProgram = $occupantToEdit->study_program;
                $this->classYear = $occupantToEdit->class_year;
    
                $this->existingIdentityCardFile = $occupantToEdit->identity_card_file;
                $this->existingCommunityCardFile = $occupantToEdit->community_card_file;
            }
        } else {
            // When adding a new occupant, ensure occupantIdBeingSelected is null
            $this->occupantIdBeingSelected = null;
            // Clear any existing file references for a new form
            $this->existingIdentityCardFile = null;
            $this->existingCommunityCardFile = null;
        }
    }

    public function saveOccupant()
    {
        // Validate basic fields first
        $fieldsToValidate = [
            'fullName', 'email', 'whatsappNumber'
        ];
        if ($this->isStudent) {
            $fieldsToValidate = array_merge($fieldsToValidate, ['studentId', 'faculty', 'studyProgram', 'classYear']);
        }
        $this->validate(collect($this->rules())->only($fieldsToValidate)->toArray());

        // Validate files based on their current state and whether it's an update or new creation
        $this->validateOnly('identityCardFile', $this->rules());
        $this->validateOnly('communityCardFile', $this->rules());

        $isAddingNewOccupant = is_null($this->occupantIdBeingSelected);
        
        // Check if the current logged-in occupant is the PIC of this contract
        $isCurrentUserPic = $this->contract && $this->contract->pic->isNotEmpty() && $this->contract->pic->first()->id === Auth::guard('occupant')->user()->id;

        // Capacity and PIC Authorization Check (for adding new occupants)
        if ($isAddingNewOccupant) {
            if (!$isCurrentUserPic) {
                LivewireAlert::
                error()->title('Akses Ditolak!')
                    ->text('Hanya penanggung jawab kontrak (PIC) yang dapat menambahkan penghuni lain.')
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }

            // Check if a new occupant (by email) would exceed capacity
            $prospectiveNewOccupantCount = $this->contract->occupants->count();
            if (Occupant::where('email', $this->email)->doesntExist()) {
                $prospectiveNewOccupantCount++; // If email doesn't exist, this will be a truly new occupant
            }

            if ($this->contract && $this->unit && $prospectiveNewOccupantCount > $this->unit->capacity) {
                LivewireAlert::title('Kapasitas Unit Penuh!')
                    ->text('Tidak dapat menambahkan penghuni baru. Jumlah penghuni sudah mencapai kapasitas maksimal unit ini (' . $this->unit->capacity . ' orang).')
                    ->error()
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
        }
        
        // Prepare occupant data for update/create
        $data = [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsappNumber,
            'gender' => $this->gender,
            'status' => OccupantStatus::PENDING_VERIFICATION, // Always set to pending verification on save

            'is_student' => $this->isStudent,
            'student_id' => $this->isStudent ? $this->studentId : null,
            'faculty' => $this->isStudent ? $this->faculty : null,
            'study_program' => $this->isStudent ? $this->studyProgram : null,
            'class_year' => $this->isStudent ? $this->classYear : null,
        ];

        // Handle Identity Card File upload/retention/deletion
        $currentIdentityCardPath = $this->occupantIdBeingSelected ? Occupant::find($this->occupantIdBeingSelected)->identity_card_file : null;
        if ($this->identityCardFile instanceof TemporaryUploadedFile) {
            if ($currentIdentityCardPath) { Storage::disk('public')->delete($currentIdentityCardPath); }
            $data['identity_card_file'] = $this->identityCardFile->store('occupant', 'public');
        } elseif (is_null($this->identityCardFile) && $currentIdentityCardPath) {
            $data['identity_card_file'] = null; // User explicitly removed it
            Storage::disk('public')->delete($currentIdentityCardPath);
        } elseif (!$isAddingNewOccupant && $currentIdentityCardPath) {
            $data['identity_card_file'] = $currentIdentityCardPath; // Keep existing if not changed during update
        } else {
            $data['identity_card_file'] = null; // New occupant, no file or no existing
        }

        // Handle Community Card File upload/retention/deletion
        $currentCommunityCardPath = $this->occupantIdBeingSelected ? Occupant::find($this->occupantIdBeingSelected)->community_card_file : null;
        if ($this->communityCardFile instanceof TemporaryUploadedFile) {
            if ($currentCommunityCardPath) { Storage::disk('public')->delete($currentCommunityCardPath); }
            $data['community_card_file'] = $this->communityCardFile->store('occupant', 'public');
        } elseif (is_null($this->communityCardFile) && $currentCommunityCardPath) {
            $data['community_card_file'] = null; // User explicitly removed it
            Storage::disk('public')->delete($currentCommunityCardPath);
        } elseif (!$isAddingNewOccupant && $currentCommunityCardPath) {
            $data['community_card_file'] = $currentCommunityCardPath; // Keep existing if not changed during update
        } else {
            $data['community_card_file'] = null; // New occupant, no file or no existing
        }

        // Create or Update Occupant record in the `occupants` table
        $occupant = Occupant::updateOrCreate(
            ['email' => $this->email],
            $data
        );

        // Attach occupant to the current contract if not already attached
        if ($isAddingNewOccupant) { // This condition implies a new occupant record or a new association for an existing record
            if (!$this->contract->occupants->contains($occupant->id)) {
                // Attach the occupant to the contract as a non-PIC member
                $this->contract->occupants()->attach($occupant->id, ['is_pic' => false]);
            }
        }
        // If it's an update to an existing occupant who is already part of this contract, no re-attachment is needed.

        LivewireAlert::title('Data berhasil diperbarui. Menunggu verifikasi.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        $this->showModal = false;
        
        // Re-fetch all component data to update the UI with latest information
        $this->mount();
        $this->reset(['occupantIdBeingSelected']); // Reset selected ID to ensure 'add new' mode for next open
    }

    public function removeIdentityCardFile()
    {
        if ($this->existingIdentityCardFile) {
            Storage::disk('public')->delete($this->existingIdentityCardFile);
            $this->identityCardFile = null; // Clear the property for Livewire
            $this->existingIdentityCardFile = null; // Clear the existing file reference
            LivewireAlert::info()->text('File KTP berhasil dihapus.')->toast()->position('top-end')->show();
        }
    }

    public function removeCommunityCardFile()
    {
        if ($this->existingCommunityCardFile) {
            Storage::disk('public')->delete($this->existingCommunityCardFile);
            $this->communityCardFile = null; // Clear the property for Livewire
            $this->existingCommunityCardFile = null; // Clear the existing file reference
            LivewireAlert::info()->text('File Kartu Komunitas berhasil dihapus.')->toast()->position('top-end')->show();
        }
    }

    public function savePayment()
    {
        $fieldsToValidate = [
            'amount_paid', 'proofOfPayment', 'notes'
        ];

        $this->validate(collect($this->rules())
            ->only($fieldsToValidate)
            ->toArray()
        );

        // Simulate file upload path (replace with actual storage logic)
        $path = $this->proofOfPayment->store('payments', 'public');

        Payment::create([
            'invoice_id' => $this->latestInvoice->id, // Pastikan latestInvoice tidak null di sini
            'amount_paid' => $this->amount_paid,
            'payment_date' => now(),
            'proof_of_payment_path' => $path,
            'notes' => $this->notes,
            'status' => \App\Enums\PaymentStatus::PENDING_VERIFICATION, // Menggunakan PENDING_VERIFICATION dari enum PaymentStatus
        ]);

        $this->latestInvoice->status = InvoiceStatus::PENDING_PAYMENT_VERIFICATION;
        $this->latestInvoice->save();

        LivewireAlert::title('Berhasil')
            ->text('Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi.')
            ->success()
            ->timer(1000) // Optional: Show for 3 seconds
            ->show();

        $this->reset(['proofOfPayment', 'notes']);
        $this->showModal = false;
        $this->modalType = '';
    }

    public function updatedFaculty($value)
    {
        $this->studyProgramOptions = collect(AcademicData::getFacultiesAndPrograms()[$value] ?? [])->map(fn($sp) => ['value' => $sp, 'label' => $sp])->toArray();
        $this->studyProgram = null; // Reset study program when faculty changes
    }

    public function resetAll()
    {
        $this->reset(['proofOfPayment', 'notes', 'faculty', 'studyProgram']);
        $this->showModal = false;
        $this->modalType = '';
        $this->occupantIdBeingSelected = null;
        // Do NOT reset $this->occupant or $this->contract here, they are the main context
        $this->fullName = '';
        $this->email = '';
        $this->whatsappNumber = '';
        $this->identityCardFile = null;
        $this->communityCardFile = null;
        $this->existingIdentityCardFile = null;
        $this->existingCommunityCardFile = null;
        $this->isStudent = false;
        $this->studentId = '';
        $this->faculty = '';
        $this->studyProgram = '';
        $this->classYear = '';
    }
}
