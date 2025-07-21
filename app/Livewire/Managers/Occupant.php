<?php

namespace App\Livewire\Managers;

use App\Data\AcademicData;
use App\Enums\GenderAllowed;
use App\Enums\OccupantStatus;
use App\Exports\OccupantsExport;
use App\Models\Contract;
use App\Models\Occupant as OccupantModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LivewireFilepond\WithFilePond;

class Occupant extends Component
{
    use WithPagination;
    use WithFileUploads;
    use WithFilePond;

    // Main data properties
    public
        $fullName = '',
        $email = '',
        $whatsappNumber = '',
        $gender = '',
        $identityCardFile = null,
        $communityCardFile = null,
        $agreeToRegulation = false,
        $notes = '',
        $status = '';

    public bool $isStudent = false;

    public
        $studentId = '',
        $faculty = '',
        $studyProgram = '',
        $classYear = '';

    // Relational data properties
    public
        $contracts = [],
        $contractIds = [];

    // Temporary file properties for uploads
    public
        $temporaryIdentityCardFile,
        $temporaryCommunityCardFile;

    // Options
    public
        $statusOptions,
        $genderOptions,
        $facultyOptions,
        $studyProgramOptions,
        $classYearOptions,
        $contractOptions;

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
    public $occupantIdBeingSelected = null;

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
        $this->genderOptions = GenderAllowed::optionsWithoutGeneral();

        $this->facultyOptions = $this->convertToOptions(
            collect(AcademicData::getFacultiesAndPrograms())->keys()->toArray()
        );

        $this->classYearOptions = $this->convertToOptions(
            range(date('Y'), date('Y') - 7)
        );

        $this->contractOptions = Contract::query()
            ->with(['unit.unitCluster'])
            ->get()
            ->map(function ($contract) {
                return [
                    'value' => $contract->id,
                    'label' => $contract->contract_code . ' - ' . $contract->unit->unitCluster->name . ' | ' . $contract->unit->room_number,
                ];
            })->toArray();
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
        $this->occupantIdBeingSelected = $occupant->id;
        $this->fullName = $occupant->full_name;
        $this->email = $occupant->email;
        $this->whatsappNumber = $occupant->whatsapp_number;
        $this->gender = $occupant->gender;
        $this->identityCardFile = $occupant->identity_card_file;
        $this->communityCardFile = $occupant->community_card_file;
        $this->agreeToRegulation = $occupant->agree_to_regulation;
        $this->notes = $occupant->notes;
        $this->status = $occupant->status->value;

        if ($occupant->is_student) {
            $this->isStudent = true;
            $this->studentId = $occupant->student_id;
            $this->faculty = $occupant->faculty;
            $this->updatedFaculty($this->faculty);
            $this->studyProgram = $occupant->study_program;
            $this->classYear = $occupant->class_year;
        }

        $this->contracts = $occupant->contracts;
        $this->contractIds = $occupant->contracts->pluck('id')->toArray();

        // Filling files data
        $this->temporaryIdentityCardFile = $this->identityCardFile;
        $this->temporaryCommunityCardFile = $this->communityCardFile;
    }

    public function rules()
    {
        $rules = [
            'fullName' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsappNumber' => 'nullable|string|max:20',
            // 'agreeToRegulation' => 'accepted',
            'notes' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(OccupantStatus::values())],

            'identityCardFile' => $this->occupantIdBeingSelected && $this->identityCardFile === $this->temporaryIdentityCardFile
                ? 'nullable'
                : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',

            'communityCardFile' => $this->occupantIdBeingSelected && $this->communityCardFile === $this->temporaryCommunityCardFile
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

    public function messages()
    {
        return [
            'fullName.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'whatsappNumber.max' => 'Nomor WhatsApp tidak boleh lebih dari 20 karakter.',
            'gender.required' => 'Jenis kelamin wajib diisi.',
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
            'gender' => $this->gender,
            'agree_to_regulation' => $this->agreeToRegulation,
            'notes' => $this->notes,
            'status' => $this->status,

            'is_student'           => $this->isStudent,
            'student_id'           => $this->isStudent ? $this->studentId : null,
            'faculty'              => $this->isStudent ? $this->faculty : null,
            'study_program'        => $this->isStudent ? $this->studyProgram : null,
            'class_year'           => $this->isStudent ? $this->classYear : null,
        ];

        if ($this->identityCardFile !== $this->temporaryIdentityCardFile && $this->temporaryIdentityCardFile != null) {
            Storage::disk('public')->delete($this->temporaryIdentityCardFile);
            $data['identity_card_file'] = null;
        }

        if ($this->communityCardFile !== $this->temporaryCommunityCardFile && $this->temporaryCommunityCardFile != null) {
            Storage::disk('public')->delete($this->temporaryCommunityCardFile);
            $data['community_card_file'] = null;
        }

        if ($this->identityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingSelected && $this->temporaryIdentityCardFile != null) {
                if ($this->identityCardFile !== $this->temporaryIdentityCardFile) {
                    Storage::disk('public')->delete($this->temporaryIdentityCardFile);
                }
            }

            $data['identity_card_file'] = $this->identityCardFile->store('occupant', 'public');
        }

        if ($this->communityCardFile instanceof TemporaryUploadedFile) {
            if ($this->occupantIdBeingSelected && $this->temporarycommunityCardFile != null) {
                if ($this->communityCardFile !== $this->temporarycommunityCardFile) {
                    Storage::disk('public')->delete($this->temporarycommunityCardFile);
                }
            }

            $data['community_card_file'] = $this->communityCardFile->store('occupant', 'public');
        }

        $occupant = OccupantModel::updateOrCreate(['id' => $this->occupantIdBeingSelected], $data);

        $occupant->contracts()->sync($this->contractIds);

        LivewireAlert::title($this->occupantIdBeingSelected ? 'Data berhasil diperbarui.' : 'Unit berhasil ditambahkan.')
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
            'gender',
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
            'contracts',
            'contractIds',
        ]);

        $this->occupantIdBeingSelected = null;
        $this->showModal = false;
    }

    public function updatedFaculty($facultyName)
    {
        if (!empty($facultyName)) {
            $this->studyProgramOptions = $this->convertToOptions(
                AcademicData::getFacultiesAndPrograms()[$facultyName] ?? []
            );
        } else {
            $this->studyProgramOptions = $this->convertToOptions([]);
        }
        $this->reset('studyProgram');
    }

    private function convertToOptions($array)
    {
        return collect($array)->map(function ($item) {
            return ['value' => $item, 'label' => $item];
        })->toArray();
    }

    public function exportPdf()
    {
        // Validate the search and filter parameters
        $occupants = $this->buildUnitQuery()->get();

        // Prepare data for PDF export
        $pdfData = $occupants->map(function ($occupant) {
            return [
                'full_name' => $occupant->full_name,
                'email' => $occupant->email,
                'whatsapp_number' => $occupant->whatsapp_number,
                'gender' => $occupant->gender,
                'status' => $occupant->status->label(),
                'is_student' => $occupant->is_student ? 'Ya' : 'Tidak',
                'student_id' => $occupant->student_id,
                'faculty' => $occupant->faculty,
                'study_program' => $occupant->study_program,
                'class_year' => $occupant->class_year,
                'contracts' => $occupant->contracts->map(function ($contract) {
                    return $contract->contract_code . ' - ' . $contract->unit->unitCluster->name . ' | ' . $contract->unit->room_number;
                })->implode(', '),
            ];
        });

        // Show processing alert
        LivewireAlert::title('PDF Berhasil Diunduh')
            ->text('Data penghuni berhasil diekspor ke PDF.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Load the PDF view with the data
        $pdf = Pdf::loadView('exports.occupants', ['occupants' => $pdfData]);

        // Return the PDF as a downloadable response
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, now()->format('Y-m-d') . '_occupants.pdf');
    }

    public function exportExcel()
    {
        $occupants = $this->buildUnitQuery()->get();

        LivewireAlert::title('Excel Berhasil Diunduh')
            ->text('Data penghuni berhasil diekspor ke Excel.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        return Excel::download(
            new OccupantsExport($occupants),
            now()->format('Y-m-d') . '_occupants.xlsx'
        );
    }
}
