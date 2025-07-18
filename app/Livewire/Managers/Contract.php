<?php

namespace App\Livewire\Managers;

use App\Enums\ContractStatus;
use App\Enums\PricingBasis;
use App\Models\Contract as ContractModel;
use App\Models\Occupant;
use App\Models\OccupantType;
use App\Models\Unit;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContractsExport; // Anda perlu membuat export ini nanti

class Contract extends Component
{
    use WithPagination;

    // Properti data utama
    public
        $contractCode,
        $unitId,
        $occupantIds, // Untuk multiple select
        $occupantTypeId,
        $totalPrice,
        $startDate,
        $endDate,
        $pricingBasis,
        $notes,
        $status;

    // Opsi dropdown
    public
        $statusOptions,
        $unitOptions,
        $occupantOptions,
        $occupantTypeOptions,
        $pricingBasisOptions;

    // Properti filter
    public $search = '';
    public $statusFilter = '';

    // Properti paginasi dan pengurutan
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    // Properti modal
    public $showModal = false;
    public $modalType = '';
    public $contractIdBeingSelected = null;

    // Query string
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->statusOptions = ContractStatus::options();
        $this->pricingBasisOptions = PricingBasis::options();
        $this->occupantTypeOptions = OccupantType::query()
            ->get()
            ->map(function ($occupantType) {
                return [
                    'value' => $occupantType->id,
                    'label' => $occupantType->name,
                ];
            })->toArray();

        $this->unitOptions = Unit::query()
            ->with('unitCluster')
            ->get()
            ->map(function ($unit) {
                return [
                    'value' => $unit->id,
                    'label' => ($unit->unitCluster->name ?? 'N/A') . ' | ' . $unit->room_number,
                ];
            })->toArray();

        $this->occupantOptions = Occupant::query()
            ->get()
            ->map(function ($occupant) {
                return [
                    'value' => $occupant->id,
                    'label' => $occupant->full_name . ' (' . $occupant->email . ')',
                ];
            })->toArray();

        $this->occupantIds = []; // Initialize as an empty array for multiple select
    }

    private function buildContractQuery()
    {
        return ContractModel::query()
            ->with(['unit.unitCluster', 'occupants', 'occupantType'])
            ->when($this->search, function ($query) {
                $query->where('contract_code', 'like', '%' . $this->search . '%')
                    ->orWhere('total_price', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%')
                    ->orWhereHas('occupants', function ($q) {
                        $q->where('full_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('unit', function ($q) {
                        $q->where('room_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('unit.unitCluster', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->orderBy, $this->sort);
    }

    public function render()
    {
        $contracts = $this->buildContractQuery()->paginate($this->perPage);

        return view('livewire.managers.tenancy.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(ContractModel $contract)
    {
        $this->fillData($contract);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function detail(ContractModel $contract)
    {
        $this->fillData($contract);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    public function fillData(ContractModel $contract)
    {
        $this->contractIdBeingSelected = $contract->id;
        $this->contractCode = $contract->contract_code;
        $this->unitId = $contract->unit_id;
        $this->occupantIds = $contract->occupants->pluck('id')->toArray();
        $this->occupantTypeId = $contract->occupant_type_id;
        $this->totalPrice = $contract->total_price;
        $this->startDate = $contract->start_date ? $contract->start_date->format('Y-m-d') : null;
        $this->endDate = $contract->end_date ? $contract->end_date->format('Y-m-d') : null;
        $this->pricingBasis = $contract->pricing_basis->value;
        $this->notes = $contract->notes;
        $this->status = $contract->status->value;
    }

    public function rules()
    {
        return [
            'contractCode' => 'nullable|string|max:255|unique:contracts,contract_code,' . $this->contractIdBeingSelected,
            'unitId' => 'required|exists:units,id',
            'occupantIds' => 'required|array|min:1',
            'occupantIds.*' => 'exists:occupants,id',
            'occupantTypeId' => 'required|exists:occupant_types,id',
            'totalPrice' => 'required|numeric|min:0',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'pricingBasis' => ['required', \Illuminate\Validation\Rule::in(PricingBasis::values())],
            'notes' => 'nullable|string|max:500',
            'status' => ['required', \Illuminate\Validation\Rule::in(ContractStatus::values())],
        ];
    }

    public function messages()
    {
        return [
            'contractCode.unique' => 'Kode kontrak ini sudah digunakan.',
            'unitId.required' => 'Unit wajib dipilih.',
            'unitId.exists' => 'Unit yang dipilih tidak valid.',
            'occupantIds.required' => 'Setidaknya satu penghuni wajib dipilih.',
            'occupantIds.array' => 'Penghuni harus dalam format array.',
            'occupantIds.min' => 'Setidaknya satu penghuni wajib dipilih.',
            'occupantIds.*.exists' => 'Salah satu penghuni yang dipilih tidak valid.',
            'occupantTypeId.required' => 'Tipe penghuni wajib dipilih.',
            'occupantTypeId.exists' => 'Tipe penghuni yang dipilih tidak valid.',
            'totalPrice.required' => 'Total harga wajib diisi.',
            'totalPrice.numeric' => 'Total harga harus berupa angka.',
            'totalPrice.min' => 'Total harga tidak boleh kurang dari 0.',
            'startDate.required' => 'Tanggal mulai kontrak wajib diisi.',
            'startDate.date' => 'Tanggal mulai kontrak tidak valid.',
            'endDate.required' => 'Tanggal berakhir kontrak wajib diisi.',
            'endDate.date' => 'Tanggal berakhir kontrak tidak valid.',
            'endDate.after_or_equal' => 'Tanggal berakhir kontrak tidak boleh sebelum tanggal mulai.',
            'pricingBasis.required' => 'Dasar harga wajib dipilih.',
            'pricingBasis.in' => 'Dasar harga yang dipilih tidak valid.',
            'status.required' => 'Status kontrak wajib diisi.',
            'status.in' => 'Status kontrak yang dipilih tidak valid.',
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
            'contract_code' => $this->contractCode,
            'unit_id' => $this->unitId,
            'occupant_type_id' => $this->occupantTypeId,
            'total_price' => $this->totalPrice,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'pricing_basis' => $this->pricingBasis,
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        $contract = ContractModel::updateOrCreate(['id' => $this->contractIdBeingSelected], $data);

        // Sinkronkan penghuni terkait
        $contract->occupants()->sync($this->occupantIds);

        LivewireAlert::title($this->contractIdBeingSelected ? 'Data kontrak berhasil diperbarui.' : 'Kontrak berhasil ditambahkan.')
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
            'contractCode',
            'unitId',
            'occupantIds',
            'occupantTypeId',
            'totalPrice',
            'startDate',
            'endDate',
            'pricingBasis',
            'notes',
            'status',
        ]);
        $this->contractIdBeingSelected = null;
        $this->showModal = false;
    }

    public function confirmDelete(ContractModel $contract)
    {
        $this->contractIdBeingSelected = $contract->id;
        LivewireAlert::confirm('Apakah Anda yakin ingin menghapus kontrak ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua invoice terkait!', [
            'confirmButtonText' => 'Ya, Hapus!',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onDismissed' => 'cancelDelete',
            'position' => 'center',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonColor' => '#EF4444',
            'cancelButtonColor' => '#6B7280',
        ]);
    }

    public function delete()
    {
        if ($this->contractIdBeingSelected) {
            ContractModel::find($this->contractIdBeingSelected)->delete();
            LivewireAlert::success('Kontrak berhasil dihapus.')->toast();
            $this->reset(['contractIdBeingSelected']);
        }
    }

    public function cancelDelete()
    {
        $this->reset(['contractIdBeingSelected']);
    }

    public function exportPdf()
    {
        $contracts = $this->buildContractQuery()->get();

        LivewireAlert::title('PDF Berhasil Diunduh')
            ->text('Data kontrak berhasil diekspor ke PDF.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Anda perlu membuat resources/views/exports/contracts.blade.php
        $pdf = Pdf::loadView('exports.contracts', ['contracts' => $contracts]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, now()->format('Y-m-d') . '_contracts.pdf');
    }

    public function exportExcel()
    {
        $contracts = $this->buildContractQuery()->get();

        LivewireAlert::title('Excel Berhasil Diunduh')
            ->text('Data kontrak berhasil diekspor ke Excel.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Anda perlu membuat App\Exports\ContractsExport.php
        return Excel::download(
            new ContractsExport($contracts),
            now()->format('Y-m-d') . '_contracts.xlsx'
        );
    }
}
