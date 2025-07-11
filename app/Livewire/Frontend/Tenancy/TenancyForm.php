<?php

namespace App\Livewire\Frontend\Tenancy;

use App\Enums\GenderAllowed;
use App\Models\UnitCluster;
use App\Models\OccupantType;
use App\Models\Regulation;
use App\Models\UnitType;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Data\AcademicData;

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

            // Rules step 2
            'fullName'         => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'whatsappNumber'   => 'required|string|max:15',
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
        // Validasi bahwa checkbox persetujuan harus dicentang
        $this->validateOnly(field: 'agreeToRegulations');

        // Simpan data ke table Occupants -> membutuhkan data contract

        // Simpan data ke table Contracts -> membutuhkan data Unit dan Occupant sebagai pic yang sudah diisi sebelumnya

        // Membuat login dengan kode kontrak


        // Lanjutkan ke langkah terakhir (halaman sukses)
        $this->currentStep = 4;
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
