<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\ContractStatus;
use App\Enums\GenderAllowed;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Jobs\SendWelcomeEmail;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\UnitCluster;
use App\Models\OccupantType;
use App\Models\Regulation;
use App\Models\UnitType;
use App\Models\Unit;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Data\AcademicData;
use App\Enums\UnitStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class TenancyForm extends Component
{
    use WithFileUploads;

    // Initial step
    public int $currentStep = 1;

    // Inherited properties
    public
        $occupantType,
        $pricingBasis,
        $startDate,
        $endDate,
        $unitType,
        $price,
        $totalDays,
        $totalPrice;

    // History URLs
    public
        $filterUrl,
        $detailUrl;

    public
        $totalUnits = 0;

    public
        $genderSelected = GenderAllowed::MALE->value,
        $unitClusterSelectedId;

    public
        $unit,
        $unitCluster;

    public $regulations;

    public $studentForm = false;

    public
        $genderAllowedOptions,
        $unitClusterOptions,
        $unitOptions;

    public
        $facultyOptions = [],
        $studyProgramOptions = [],
        $classYearOptions = [];

    // STEP 1
    public $unitId;

    // STEP 2
    public
        $fullName,
        $email,
        $whatsappNumber,
        $identityCardFile,
        $communityCardFile;
    // Properti untuk verifikasi mahasiswa
    public bool $isStudent = false;
    public
        $studentId,
        $faculty,
        $studyProgram,
        $classYear;

    // STEP 3
    public bool $agreeToRegulations = false;

    // STEP 4
    public $authUrl;

    public function mount()
    {
        if (!session()->has('tenancy_data')) {
            return redirect()->route('home');
        }

        // Load tenancy data from session
        $tenancyData = session('tenancy_data', []);

        // Initialize properties with tenancy data
        $this->occupantType = isset($tenancyData['occupantType']) ? OccupantType::find($tenancyData['occupantType']) : null;
        $this->pricingBasis = $tenancyData['pricingBasis'] ?? null;
        $this->startDate = $tenancyData['startDate'] ?? null;
        $this->endDate = $tenancyData['endDate'] ?? null;
        $this->unitType = isset($tenancyData['unitType']) ? UnitType::find($tenancyData['unitType']) : null;
        $this->price = $tenancyData['price'] ?? null;
        $this->totalDays = $tenancyData['totalDays'] ?? null;
        $this->totalPrice = $tenancyData['totalPrice'] ?? null;

        $this->filterUrl = $tenancyData['filterUrl'] ?? null;
        $this->detailUrl = $tenancyData['detailUrl'] ?? null;

        // Step 1 Options
        $this->genderAllowedOptions = GenderAllowed::optionsWithoutGeneral();
        $this->unitClusterOptions = $this->occupantType?->accessibleClusters()->get() ?? collect();

        $this->unitClusterSelectedId = $this->unitClusterOptions->first()?->id ?? null;
        $this->findUnits();

        // Step 2 Options
        $this->facultyOptions = array_keys(AcademicData::getFacultiesAndPrograms());
        $this->classYearOptions = range(date('Y'), date('Y') - 7);

        // Load regulations
        $this->regulations = Regulation::orderBy('priority')->get();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'unitClusterSelectedId' || $propertyName === 'genderSelected') {
            $this->findUnits();
        }

        if (in_array($propertyName, ['unitId', 'fullName', 'email', 'whatsappNumber', 'identityCardFile', 'communityCardFile'])) {
            $this->validateOnly($propertyName);
        }

        if (in_array($propertyName, ['studentId', 'faculty', 'studyProgram', 'classYear'])) {
            $this->validateOnly($propertyName);
        }

        if ($propertyName === 'agreeToRegulations') {
            $this->validateOnly($propertyName);
        }
    }

    public function render()
    {
        return view('livewire.frontend.tenancy.form.index');
    }

    public function findUnits()
    {
        $filters = [
            'occupantTypeId' => $this->occupantType?->id,
            'genderAllowed' => $this->genderSelected,
            'unitClusterId' => $this->unitClusterSelectedId,
        ];

        $units = Unit::query()
            ->availableWithFilters($filters)
            ->when($this->unitType, function ($query) {
                $query->where('unit_type_id', $this->unitType->id);
            })
            ->get();

        $this->totalUnits = $units->count();

        $this->unitOptions = $units;
    }

    protected function rules()
    {
        return [
            // Rules step 1
            'unitId'           => 'required',
            'genderSelected' => 'required|in:' . implode(',', GenderAllowed::withoutGeneralValues()),

            // Rules step 2
            'fullName'         => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'whatsappNumber'   => 'required|string|min:5|max:15',
            'identityCardFile' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'communityCardFile'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',

            // Conditional Rules for Students
            'studentId'        => 'required_if:isStudent,true',
            'faculty'        => 'required_if:isStudent,true',
            'studyProgram'   => 'required_if:isStudent,true',
            'classYear'        => 'required_if:isStudent,true',

            // Rules step 3
            'agreeToRegulations' => 'accepted',
        ];
    }

    protected function messages()
    {
        return [
            'unitId.required'           => 'Silakan pilih unit kamar terlebih dahulu.',
            'genderSelected.required'   => 'Jenis kelamin wajib dipilih.',
            'fullName.required'         => 'Nama lengkap wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'whatsappNumber.required'   => 'Nomor WhatsApp wajib diisi.',
            'identityCardFile.required' => 'Kartu identitas wajib di-upload.',
            'identityCardFile.mimes'    => 'Format file harus .jpg, .jpeg, .png, atau .pdf.',
            'identityCardFile.max'      => 'Ukuran file maksimal 2MB.',
            'communityCardFile.mimes'   => 'Format file harus .jpg, .jpeg, .png, atau .pdf.',
            'communityCardFile.max'     => 'Ukuran file maksimal 2MB.',
            'studentId.required_if'     => 'ID mahasiswa wajib diisi jika Anda adalah mahasiswa.',
            'faculty.required_if'     => 'Fakultas wajib dipilih.',
            'studyProgram.required_if'=> 'Program studi wajib dipilih.',
            'classYear.required_if'     => 'Tahun angkatan wajib dipilih.',
            'agreeToRegulations.accepted' => 'Anda harus menyetujui tata tertib untuk melanjutkan.',
        ];
    }


    public function firstStepSubmit()
    {
        $this->validateOnly('unitId');
        $this->validateOnly('genderSelected');

        $this->unit = Unit::find($this->unitId);
        $this->unitCluster = UnitCluster::find($this->unitClusterSelectedId);

        $this->currentStep = 2;
    }

    public function secondStepSubmit()
    {
        $fieldsToValidate = [
            'fullName', 'email', 'whatsappNumber',
            'identityCardFile', 'communityCardFile'
        ];

        if ($this->isStudent) {
            $fieldsToValidate = array_merge($fieldsToValidate, ['studentId', 'faculty', 'studyProgram', 'classYear']);
        }

        if (!empty($this->whatsappNumber)) {
            $cleanNumber = preg_replace('/^(\+62|62|0)/', '', $this->whatsappNumber);
            $this->whatsappNumber = '62' . $cleanNumber;
        }

        $this->validate(collect($this->rules())
            ->only($fieldsToValidate)
            ->toArray()
        );

        $this->currentStep = 3;
    }

    public function thirdStepSubmit()
    {
        // Validate the agreeToRegulations field
        $this->validateOnly(field: 'agreeToRegulations');

        DB::beginTransaction();

        try {
            // Handle Occupant creation or update

            $identityPath = $this->identityCardFile->store('occupant', 'public');
            $communityPath = $this->communityCardFile ? $this->communityCardFile->store('occupant', 'public') : null;

            $occupant = Occupant::updateOrCreate(
                ['email' => $this->email], // Kunci untuk mencari
                [
                    'full_name'            => $this->fullName,
                    'whatsapp_number'      => $this->whatsappNumber,
                    'gender'               => $this->genderSelected,
                    'agree_to_regulations' => $this->agreeToRegulations,
                    'status'               => $this->occupantType->requires_verification ? OccupantStatus::PENDING_VERIFICATION : OccupantStatus::VERIFIED,
                    'is_student'           => $this->isStudent,
                    'student_id'           => $this->isStudent ? $this->studentId : null,
                    'faculty'              => $this->isStudent ? $this->faculty : null,
                    'study_program'        => $this->isStudent ? $this->studyProgram : null,
                    'class_year'           => $this->isStudent ? $this->classYear : null,

                    'identity_card_file'   => $identityPath,
                    'community_card_file'  => $communityPath,
                ]
            );

            // Handle Contract creation
            $contract = Contract::create([
                'contract_code' => Contract::generateContractCode(
                    $this->unitCluster,
                    $this->occupantType,
                    $this->pricingBasis->value
                ),
                'contract_pic' => $occupant->id,
                'unit_id' => $this->unit->id,
                'occupant_type_id' => $this->occupantType->id,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'pricing_basis' => $this->pricingBasis->value,
                'total_price' => $this->totalPrice,
                'status' => ContractStatus::PENDING_PAYMENT,
            ]);

            $contract->occupants()->attach($occupant->id);

            $invoice = null;
            if ($occupant->status === OccupantStatus::VERIFIED) {
                // Handle Invoice creation
                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'contract_id' => $contract->id,
                    'description' => 'Pembayaran sewa pertama untuk unit ' . $this->unit->room_number,
                    'amount' => $this->totalPrice,
                    'due_at' => Carbon::now()->addHours(config('tenancy.initial_payment_due_hours')),
                    'status' => InvoiceStatus::UNPAID,
                ]);
            }

            // Update unit status to OCCUPIED
            $this->unit->status = UnitStatus::OCCUPIED;
            $this->unit->save();

            // Create a signed URL for contract login
            $this->authUrl = URL::temporarySignedRoute(
                'contract.auth.url',
                now()->addHours(value: 1),
                ['data' => encrypt($contract->id)]
            );

            // Send welcome email to the contract pic
            SendWelcomeEmail::dispatch($occupant, $contract, $this->authUrl, $invoice);
            // Commit the transaction
            DB::commit();

            // Clear session data
            session()->forget('tenancy_data');
            LivewireAlert::title('Pemesanan Berhasil')
            ->success()
            ->show();
            $this->currentStep = 4;
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($identityPath) && Storage::disk('public')->exists($identityPath)) {
                Storage::disk('public')->delete($identityPath);
            }
            if (isset($communityPath) && Storage::disk('public')->exists($communityPath)) {
                Storage::disk('public')->delete($communityPath);
            }

            Log::error('Gagal membuat pesanan: ' . $e->getMessage());
            LivewireAlert::title('Terjadi Kesalahan')
                ->text('Gagal membuat pesanan: ' . $e->getMessage())
                ->error()
                ->show();
        }
    }

    public function previousStep()
    {
        $this->currentStep--;

        if ($this->currentStep === 2) {
            $this->dispatch('reinit-filepond');
        }
    }

    public function updatedIsStudent($value)
    {
        $this->isStudent = $value;

        if (!$this->isStudent) {
            $this->studentForm = false;
        } else {
            $this->studentForm = true;
        }
    }

    public function updatedFaculty($facultyName)
    {
        if (!empty($facultyName)) {
            $this->studyProgramOptions = AcademicData::getFacultiesAndPrograms()[$facultyName] ?? [];
        } else {
            $this->studyProgramOptions = [];
        }
        $this->reset('studyProgram');
    }
}