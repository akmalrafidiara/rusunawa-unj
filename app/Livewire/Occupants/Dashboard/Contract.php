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
    public $proofOfPayment;
    public $notes;

    // Tambahkan properti untuk kontrak, unit, dan pembayaran jika belum ada
    public $contract;
    public $occupant;
    public $unit;
    public $latestInvoice;
    public $invoices;
    public $payments;

    // Occupant
    public $occupantIdBeingSelected;

    public
        $fullName,
        $email,
        $whatsappNumber,
        $gender,
        $identityCardFile,
        $communityCardFile;

    public $existingIdentityCardFile;
    public $existingCommunityCardFile;

    public bool $isStudent = false;
    public $studentId, $faculty, $studyProgram, $classYear;

    public $genderOptions = [];
    public $facultyOptions = [];
    public $studyProgramOptions = [];
    public $classYearOptions = [];

    public $showModal = false;
    public $modalType = '';

    public function mount(?Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        $occupantId = Auth::guard('occupant')->user()->id;
        $this->occupant = Occupant::find($occupantId);

        if ($this->occupant) {
            $this->contract = $this->occupant->contracts()->with('unit', 'invoices', 'payments')->first();

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
            'proofOfPayment' => 'required|image|max:2048',
            'notes' => 'nullable|string|max:255',

            // Occupant Validation
            'fullName' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsappNumber' => 'nullable|string|max:20',
            'gender' => [
                'required',
                Rule::in(array_keys($this->genderOptions)),
            ],

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
            'identityCardFile.mimes' => 'File KTP harus .jpg, .jpeg, .png, atau .pdf.',
            'communityCardFile.mimes' => 'File Kartu Komunitas/KK harus .jpg, .jpeg, .png, atau .pdf.',
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
        
        if($occupantId){
            $this->occupantIdBeingSelected = $occupantId;
    
            $this->occupant = Occupant::find($occupantId);
    
            if ($this->occupant) {
                $this->fullName = $this->occupant->full_name;
                $this->email = $this->occupant->email;
                $this->whatsappNumber = $this->occupant->whatsapp_number;
                $this->gender = $this->occupant->gender;
                $this->identityCardFile = $this->occupant->identity_card_file;
                $this->communityCardFile = $this->occupant->community_card_file;
                $this->isStudent = $this->occupant->is_student;
                $this->studentId = $this->occupant->student_id;
                $this->faculty = $this->occupant->faculty;
                $this->studyProgram = $this->occupant->study_program;
                $this->classYear = $this->occupant->class_year;
    
                $this->existingIdentityCardFile = $this->occupant->identity_card_file;
                $this->existingCommunityCardFile = $this->occupant->community_card_file;
            }
        }
    }

    public function saveOccupant()
    {
        $fieldsToValidate = [
            'fullName', 'email', 'whatsappNumber',
            'identityCardFile', 'communityCardFile', 'gender'
        ];

        if ($this->isStudent) {
            $fieldsToValidate = array_merge($fieldsToValidate, ['studentId', 'faculty', 'studyProgram', 'classYear']);
        }

        $this->validate(collect($this->rules())
            ->only($fieldsToValidate)
            ->toArray()
        );

        // Update occupant data
        $data = [
            'full_name' => $this->fullName,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsappNumber,
            'gender' => $this->gender,
            'status' => OccupantStatus::PENDING_VERIFICATION,

            'is_student'           => $this->isStudent,
            'student_id'           => $this->isStudent ? $this->studentId : null,
            'faculty'              => $this->isStudent ? $this->faculty : null,
            'study_program'        => $this->isStudent ? $this->studyProgram : null,
            'class_year'           => $this->isStudent ? $this->classYear : null,
        ];

        // Handle file uploads
        if ($this->identityCardFile !== $this->existingIdentityCardFile && $this->existingIdentityCardFile != null) {
            Storage::disk('public')->delete($this->existingIdentityCardFile);
            $data['identity_card_file'] = null;
        }

        if ($this->communityCardFile !== $this->existingCommunityCardFile && $this->existingCommunityCardFile != null) {
            Storage::disk('public')->delete($this->existingCommunityCardFile);
            $data['community_card_file'] = null;
        }

        if ($this->identityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingSelected && $this->existingIdentityCardFile != null) {
                if ($this->identityCardFile !== $this->existingIdentityCardFile) {
                    Storage::disk('public')->delete($this->existingIdentityCardFile);
                }
            }

            $data['identity_card_file'] = $this->identityCardFile->store('occupant', 'public');
        }

        if ($this->communityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingSelected && $this->existingcommunityCardFile != null) {
                if ($this->communityCardFile !== $this->existingcommunityCardFile) {
                    Storage::disk('public')->delete($this->existingcommunityCardFile);
                }
            }

            $data['community_card_file'] = $this->communityCardFile->store('occupant', 'public');
        }

        $this->occupant->update($data);

        LivewireAlert::title('Data berhasil diperbarui. Menunggu verifikasi.')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        $this->showModal = false;
    }

    public function savePayment()
    {
        $fieldsToValidate = [
            'proofOfPayment', 'notes'
        ];

        $this->validate(collect($this->rules())
            ->only($fieldsToValidate)
            ->toArray()
        );

        // Simulate file upload path (replace with actual storage logic)
        $path = $this->proofOfPayment->store('payments', 'public');

        Payment::create([
            'invoice_id' => $this->latestInvoice->id, // Pastikan latestInvoice tidak null di sini
            'amount_paid' => $this->latestInvoice->amount, // Assuming full payment
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
        $this->occupant = null;
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