<?php

namespace App\Livewire\Contracts\Dashboard;

use App\Data\AcademicData;
use App\Enums\GenderAllowed;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Enums\PaymentStatus;
use App\Enums\PricingBasis;
use App\Models\Contract as ContractModel;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\Payment;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class Contract extends Component
{
    use WithFileUploads;
    use WithFilePond;

    // Main properties
    public
        $contract,
        $occupants,
        $unit,
        $latestInvoice,
        $invoices,
        $payments;

    // Properties for payment form
    public
        $invoice,
        $amount_paid,
        $proofOfPayment,
        $notes;

    // Occupant Form Properties
    public $occupantIdBeingSelected; // ID of the occupant currently being edited (can be null for new)

    public
        $fullName,
        $email,
        $whatsappNumber,
        $gender,
        $identityCardFile,
        $communityCardFile;

    public
        $existingIdentityCardFile,
        $existingCommunityCardFile;

    public bool $isStudent = false;
    public
        $studentId,
        $faculty,
        $studyProgram,
        $classYear;

    public
        $genderOptions = [],
        $facultyOptions = [],
        $studyProgramOptions = [],
        $classYearOptions = [];


    // Extension Contract
    public $newEndDate;
    public $extensionMustBePaid;
    public $extensionAmountPaid;
    public $extensionProofOfPayment;
    public $extensionNotes;

    public bool $showModal = false;
    public string $modalType = '';

    public function mount(?Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        // Ambil user contract dari Auth, lalu ambil model ContractModel
        $userContract = Auth::guard('contract')->user();
        $this->contract = ContractModel::where('id', $userContract->id ?? null)->first();

        $this->occupants = $this->contract?->occupants()->get() ?? collect();
        $this->unit = $this->contract?->unit ?? new Unit();
        $this->latestInvoice = $this->contract?->invoices()->latest()->first();
        $this->invoices = $this->contract?->invoices()->get() ?? collect();
        $this->payments = $this->contract?->payments()->get() ?? collect();

        $this->genderOptions = GenderAllowed::optionsWithoutGeneral();
        $this->facultyOptions = collect(AcademicData::getFacultiesAndPrograms())->keys()->map(fn($f) => ['value' => $f, 'label' => $f])->toArray();
        $this->classYearOptions = collect(range(date('Y'), date('Y') - 7))->map(fn($y) => ['value' => $y, 'label' => (string)$y])->toArray();

        if ($this->faculty) {
            $this->updatedFaculty($this->faculty);
        }
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.contract');
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
            'email' => 'nullable|email|max:255',
            'whatsappNumber' => 'nullable|string|max:20',
            'gender' => [
                'required',
                Rule::in(GenderAllowed::values())],

            'identityCardFile' => $this->occupantIdBeingSelected && $this->identityCardFile === $this->existingIdentityCardFile
                ? 'nullable'
                : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',

            'communityCardFile' => $this->occupantIdBeingSelected && $this->communityCardFile === $this->existingCommunityCardFile
                ? 'nullable'
                : 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
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
        $this->resetAll();
        $this->resetValidation();

        $this->showModal = true;
        $this->modalType = 'history';
    }

    public function showPaymentForm()
    {
        $this->resetAll();
        $this->resetValidation();

        $this->showModal = true;
        $this->modalType = 'payment';
    }

    public function showOccupantForm($occupantId = null)
    {
        $this->resetAll();
        $this->resetValidation();

        $this->showModal = true;
        $this->modalType = 'occupant';


        if($occupantId){
            $this->occupantIdBeingSelected = $occupantId;

            $occupantToEdit = Occupant::find($occupantId);

            if ($occupantToEdit) {
                $this->fullName = $occupantToEdit->full_name ?? null;
                $this->email = $occupantToEdit->email ?? null;
                $this->whatsappNumber = $occupantToEdit->whatsapp_number ?? null;
                $this->gender = $occupantToEdit->gender->value ?? null;
                $this->identityCardFile = $occupantToEdit->identity_card_file ?? null;
                $this->communityCardFile = $occupantToEdit->community_card_file ?? null;
                $this->isStudent = $occupantToEdit->is_student ?? null;
                $this->studentId = $occupantToEdit->student_id ?? null;
                $this->faculty = $occupantToEdit->faculty ?? null;

                // Ensure study program options are loaded if faculty is set
                if ($this->faculty) {
                    $this->studyProgramOptions = collect(AcademicData::getFacultiesAndPrograms()[$this->faculty] ?? [])->map(fn($sp) => ['value' => $sp, 'label' => $sp])->toArray();
                } else {
                    $this->studyProgramOptions = [];
                }
                $this->studyProgram = $occupantToEdit->study_program ?? null;
                $this->classYear = $occupantToEdit->class_year ?? null;

                $this->existingIdentityCardFile = $occupantToEdit->identity_card_file ?? null;
                $this->existingCommunityCardFile = $occupantToEdit->community_card_file ?? null;
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
            'fullName', 'email', 'whatsappNumber', 'gender'
        ];
        if ($this->isStudent) {
            $fieldsToValidate = array_merge($fieldsToValidate, ['studentId', 'faculty', 'studyProgram', 'classYear']);
        }
        $this->validate(collect($this->rules())->only($fieldsToValidate)->toArray());

        // Validate files based on their current state and whether it's an update or new creation
        $this->validateOnly('identityCardFile', $this->rules());
        $this->validateOnly('communityCardFile', $this->rules());

        $isAddingNewOccupant = is_null($this->occupantIdBeingSelected);

        if ($isAddingNewOccupant) {
            $prospectiveNewOccupantCount = $this->contract->occupants->count();
            if (Occupant::where('email', $this->email)->doesntExist()) {
                $prospectiveNewOccupantCount++; // If email doesn't exist, this will be a truly new occupant
            }

            if ($prospectiveNewOccupantCount > ($this->unit->capacity ?? 0)) {
                LivewireAlert::title('Kapasitas Unit Penuh!')
                    ->text('Tidak dapat menambahkan penghuni baru. Jumlah penghuni sudah mencapai kapasitas maksimal unit ini (' . ($this->unit->capacity ?? 0) . ' orang).')
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
        $currentIdentityCardPath = $this->occupantIdBeingSelected ? Occupant::find($this->occupantIdBeingSelected)->identity_card_file ?? null : null;
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
        $currentCommunityCardPath = $this->occupantIdBeingSelected ? Occupant::find($this->occupantIdBeingSelected)->community_card_file ?? null : null;
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
            if (!$this->contract->occupants->contains($occupant->id ?? null)) {
                $this->contract->occupants()->attach($occupant->id ?? null);
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
        $this->gender = '';
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

    public function getPendingOtherOccupantsProperty()
    {
        return $this->contract?->occupants
            ->where('status', OccupantStatus::PENDING_VERIFICATION)
            ->reject(fn($o) => $o->id === ($this->occupant->id ?? null));
    }

    // NEW: Computed property for rejected other occupants
    public function getRejectedOtherOccupantsProperty()
    {
        return $this->contract?->occupants
            ->where('status', OccupantStatus::REJECTED)
            ->reject(fn($o) => $o->id === ($this->occupant->id ?? null));
    }

    /**
    * ====================================================
    * INI PERPANJANGAN KONTRAAAAAAKKKK >> KELUPAAAN WKWKWK
    * ====================================================
    */

    public function updatedNewEndDate($value)
    {
        $this->extensionMustBePaid = $this->calculateExtensionCost($value);
    }

    private function calculateExtensionCost($newEndDateValue): int
    {
        if (!$newEndDateValue) {
            return 0;
        }

        $oldEndDate = Carbon::parse($this->contract->end_date);
        $newEndDate = Carbon::parse($newEndDateValue);

        if ($newEndDate->isAfter($oldEndDate)) {
            $days = $oldEndDate->diffInDays($newEndDate);
            $pricePerNight = $this->contract->total_price ?? 0;
            
            return $days * $pricePerNight;
        }

        return 0;
    }

    public function showExtendContractForm()
    {
        $this->resetAll();
        $this->resetValidation();
        
        $this->showModal = true;
        $this->modalType = 'extend';
        
        $currentEndDate = $this->contract->end_date;
        $nextDay = Carbon::parse($currentEndDate)->addDay()->toDateString();
        $this->newEndDate = $nextDay;

        $this->updatedNewEndDate($this->newEndDate);

        $this->extensionAmountPaid = null;
        $this->extensionProofOfPayment = null;
        $this->extensionNotes = '';
    }

    public function extendContract()
    {
        if ($this->contract->pricing_basis !== PricingBasis::PER_NIGHT) {
            LivewireAlert::title('Gagal')
                ->text('Hanya kontrak dengan tipe per malam yang dapat diperpanjang.')
                ->error()->toast()->position('top-end')->show();
            return;
        }

        $this->validate([
            'newEndDate' => 'required|date|after:' . $this->contract->end_date,
            'extensionAmountPaid' => 'required|numeric|min:0',
            'extensionProofOfPayment' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'extensionNotes' => 'nullable|string|max:255',
        ], [
            'newEndDate.required' => 'Tanggal akhir baru wajib diisi.',
            'newEndDate.after' => 'Tanggal akhir baru harus lebih dari tanggal akhir kontrak saat ini.',
            'extensionAmountPaid.required' => 'Jumlah pembayaran wajib diisi.',
            'extensionProofOfPayment.required' => 'Bukti pembayaran wajib diunggah.',
            'extensionProofOfPayment.file' => 'File harus berupa gambar atau PDF.',
            'extensionProofOfPayment.mimes' => 'File harus .jpg, .jpeg, .png, atau .pdf.',
            'extensionProofOfPayment.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'extensionNotes.max' => 'Catatan tidak boleh lebih dari 255 karakter.',
        ]);

        $path = null;
        if ($this->extensionProofOfPayment instanceof TemporaryUploadedFile) {
            $path = $this->extensionProofOfPayment->store('payments', 'public');
        }

        $this->contract->end_date = $this->newEndDate;
        $this->contract->save();

        $invoice = Invoice::create([
            'contract_id' => $this->contract->id,
            'amount' => $this->extensionMustBePaid,
            'due_at' => $this->newEndDate,
            'status' => InvoiceStatus::PENDING_PAYMENT_VERIFICATION,
            'description' => 'Perpanjangan kontrak #' . $this->contract->contract_code . ' hingga ' . $this->newEndDate,
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => $this->extensionAmountPaid,
            'payment_date' => now(),
            'proof_of_payment_path' => $path,
            'notes' => $this->extensionNotes,
            'status' => PaymentStatus::PENDING_VERIFICATION,
        ]);

        LivewireAlert::title('Berhasil')
            ->text('Perpanjangan kontrak berhasil diajukan dan menunggu verifikasi.')
            ->success()->toast()->position('top-end')->show();

        $this->showModal = false;
        $this->modalType = '';
        $this->mount();
    }
}
