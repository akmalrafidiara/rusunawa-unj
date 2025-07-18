<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class PaymentHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public $perPage = 10;
    public $orderBy = 'due_at'; // Urutkan berdasarkan tanggal jatuh tempo
    public $sort = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'due_at'],
        'sort' => ['except' => 'desc'],
    ];

    public function render()
    {
        $occupant = Auth::user()->occupant; // Ambil data penghuni yang sedang login

        // Pastikan penghuni memiliki data kontrak
        if (!$occupant || $occupant->contracts->isEmpty()) {
            $invoices = collect(); // Kembalikan koleksi kosong jika tidak ada kontrak
        } else {
            $contractIds = $occupant->contracts->pluck('id');

            $invoices = Invoice::query()
                ->whereIn('contract_id', $contractIds)
                ->with(['contract.unit.unitCluster']) // Eager load relationships
                ->when($this->search, function ($query) {
                    $query->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->when($this->statusFilter, function ($query) {
                    $query->where('status', $this->statusFilter);
                })
                ->orderBy($this->orderBy, $this->sort)
                ->paginate($this->perPage);
        }

        return view('livewire.occupants.dashboard.payment-history', [
            'invoices' => $invoices,
            'statusOptions' => \App\Enums\InvoiceStatus::options(), // Asumsi enum sudah ada
        ]);
    }

    // Metode untuk melihat detail invoice (opsional, bisa memicu modal)
    public function viewInvoiceDetails(Invoice $invoice)
    {
        // Logika untuk menampilkan detail invoice, misalnya membuka modal
        // Atau mengarahkan ke halaman detail invoice
        // LivewireAlert::info('Melihat detail invoice: ' . $invoice->invoice_number)->toast();
        // return redirect()->route('occupant.invoice.detail', $invoice->id); // Contoh routing
    }
}
