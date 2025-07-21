<?php

namespace App\Livewire\Managers;

use App\Enums\InvoiceStatus;
use App\Mail\InvoiceReminder;
use App\Models\Contract;
use App\Models\Invoice as InvoiceModel;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
use Illuminate\Support\Facades\Mail;

class Invoice extends Component
{
    use WithPagination;

    // Properti data utama
    public
        $invoiceNumber = '',
        $contractId = '',
        $description = '',
        $amount = '',
        $dueAt = null,
        $paidAt = null,
        $status = '';

    // Opsi dropdown
    public
        $statusOptions,
        $contractOptions;

    // Properti filter
    public $search = '';
    public $statusFilter = '';

    // Properti paginasi dan pengurutan
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc'; // Mengatur default ke 'desc' untuk data terbaru di atas

    // Properti modal
    public $showModal = false;
    public $modalType = '';
    public $invoiceIdBeingSelected = null;

    // Query string
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'], // Mengatur default ke 'desc' untuk data terbaru di atas
    ];

    public function mount()
    {
        $this->statusOptions = InvoiceStatus::options();
        $this->contractOptions = Contract::query()
            ->with('unit.unitCluster') // Eager load relationships
            ->get()
            ->map(function ($contract) {
                return [
                    'value' => $contract->id,
                    'label' => $contract->contract_code . ' - ' . ($contract->unit->unitCluster->name ?? 'N/A') . ' | ' . ($contract->unit->room_number ?? 'N/A'),
                ];
            })->toArray();
    }

    private function buildInvoiceQuery()
    {
        return InvoiceModel::query()
            ->with(['contract.occupants', 'contract.unit.unitCluster']) // Eager load necessary relationships
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('contract.occupants', function ($q) {
                        $q->where('full_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('contract.unit', function ($q) {
                        $q->where('room_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('contract.unit.unitCluster', function ($q) {
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
        $invoices = $this->buildInvoiceQuery()->paginate($this->perPage);

        return view('livewire.managers.tenancy.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $this->search = '';
        $this->modalType = 'form';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(InvoiceModel $invoice)
    {
        $this->fillData($invoice);
        $this->modalType = 'form';
        $this->showModal = true;
    }

    public function detail(InvoiceModel $invoice)
    {
        $this->fillData($invoice);
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * @var \App\Models\Invoice $invoice
     */
    public function fillData(InvoiceModel $invoice)
    {
        $this->invoiceIdBeingSelected = $invoice->id;
        $this->invoiceNumber = $invoice->invoice_number;
        $this->contractId = $invoice->contract_id;
        $this->description = $invoice->description;
        $this->amount = $invoice->amount;
        $this->dueAt = $invoice->due_at ? $invoice->due_at->format('Y-m-d') : null;
        $this->paidAt = $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : null;
        $this->status = $invoice->status->value;
    }

    public function rules()
    {
        return [
            'invoiceNumber' => 'nullable|string|max:255',
            'contractId' => 'required|exists:contracts,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'dueAt' => 'required|date',
            'paidAt' => 'nullable|date|after_or_equal:dueAt',
            'status' => ['required', \Illuminate\Validation\Rule::in(InvoiceStatus::values())],
        ];
    }

    public function messages()
    {
        return [
            'contractId.required' => 'Kontrak wajib dipilih.',
            'contractId.exists' => 'Kontrak yang dipilih tidak valid.',
            'description.required' => 'Deskripsi wajib diisi.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah tidak boleh kurang dari 0.',
            'dueAt.required' => 'Tanggal jatuh tempo wajib diisi.',
            'dueAt.date' => 'Tanggal jatuh tempo tidak valid.',
            'paidAt.date' => 'Tanggal pembayaran tidak valid.',
            'paidAt.after_or_equal' => 'Tanggal pembayaran tidak boleh sebelum tanggal jatuh tempo.',
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
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
            'invoice_number' => $this->invoiceNumber,
            'contract_id' => $this->contractId,
            'description' => $this->description,
            'amount' => $this->amount,
            'due_at' => $this->dueAt,
            'paid_at' => $this->paidAt,
            'status' => $this->status,
        ];

        $invoice = InvoiceModel::updateOrCreate(['id' => $this->invoiceIdBeingSelected], $data);

        Mail::to($invoice->contract->pic->email)->send(new InvoiceReminder($invoice, 'created'));

        LivewireAlert::title($this->invoiceIdBeingSelected ? 'Data tagihan berhasil diperbarui.' : 'Tagihan berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    public function reminder($invoiceId)
    {
        $invoice = InvoiceModel::find($invoiceId);
        Mail::to($invoice->contract->pic->email)->send(new InvoiceReminder($invoice, 'created'));
    }

    public function resetForm()
    {
        $this->reset([
            'invoiceNumber',
            'contractId',
            'description',
            'amount',
            'dueAt',
            'paidAt',
            'status',
        ]);
        $this->invoiceIdBeingSelected = null;
        $this->showModal = false;
    }

    // public function confirmDelete(InvoiceModel $invoice)
    // {
    //     $this->invoiceIdBeingSelected = $invoice->id;
    //     LivewireAlert::confirm('Apakah Anda yakin ingin menghapus tagihan ini?', [
    //         'confirmButtonText' => 'Ya, Hapus!',
    //         'cancelButtonText' => 'Batal',
    //         'onConfirmed' => 'delete',
    //         'onDismissed' => 'cancelDelete',
    //         'position' => 'center',
    //         'showConfirmButton' => true,
    //         'showCancelButton' => true,
    //         'confirmButtonColor' => '#EF4444',
    //         'cancelButtonColor' => '#6B7280',
    //     ]);
    // }

    // public function delete()
    // {
    //     if ($this->invoiceIdBeingSelected) {
    //         InvoiceModel::find($this->invoiceIdBeingSelected)->delete();
    //         LivewireAlert::title('Tagihan berhasil dihapus.')->success()->toast();
    //         $this->reset(['invoiceIdBeingSelected']);
    //     }
    // }

    // public function cancelDelete()
    // {
    //     $this->reset(['invoiceIdBeingSelected']);
    // }

    public function exportPdf()
    {
        $invoices = $this->buildInvoiceQuery()->get();

        LivewireAlert::title('PDF Berhasil Diunduh')
            ->text('Data tagihan berhasil diekspor ke PDF.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $pdf = Pdf::loadView('exports.invoices', ['invoices' => $invoices]); // Anda perlu membuat exports.invoices.blade.php
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, now()->format('Y-m-d') . '_invoices.pdf');
    }

    public function exportExcel()
    {
        $invoices = $this->buildInvoiceQuery()->get();

        LivewireAlert::title('Excel Berhasil Diunduh')
            ->text('Data tagihan berhasil diekspor ke Excel.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        return Excel::download(
            new InvoicesExport($invoices), // Anda perlu membuat App\Exports\InvoicesExport.php
            now()->format('Y-m-d') . '_invoices.xlsx'
        );
    }
}
