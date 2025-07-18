<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\Payment;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Contract extends Component
{
    use WithFileUploads;

    public $invoice;
    public $proofOfPayment;
    public $notes;
    public $showPaymentModal = false;

    // Tambahkan properti untuk kontrak, unit, dan pembayaran jika belum ada
    public $contract;
    public $occupant;
    public $unit;
    public $latestInvoice;
    public $invoices;
    public $payments;

    public $showModal = false;
    public $modalType = '';

    public function mount(Invoice $invoice = null)
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
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.contract');
    }

    protected function rules()
    {
        return [
            'proofOfPayment' => 'required|image|max:2048',
            'notes' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'proofOfPayment.required' => 'Bukti pembayaran harus diunggah.',
            'proofOfPayment.image' => 'File yang diunggah harus berupa gambar.',
            'proofOfPayment.max' => 'Ukuran file tidak boleh lebih dari 2MB.',
            'notes.max' => 'Catatan tidak boleh lebih dari 255 karakter.',
        ];
    }

    public function openPaymentModal()
    {
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $this->validate();

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
        $this->showPaymentModal = false; // Close the modal after successful submission
    }
}
