<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Component;
use Livewire\WithFileUploads; // Import trait untuk upload file
use Illuminate\Validation\Rule;

class UploadPaymentProof extends Component
{
    use WithFileUploads; // Gunakan trait ini

    public Invoice $invoice; // Invoice yang akan dibayarkan
    public $proofOfPayment; // Untuk file bukti pembayaran
    public ?string $notes = null; // Catatan opsional dari penghuni

    // Aturan validasi
    protected function rules()
    {
        return [
            'proofOfPayment' => 'required|image|max:2048', // Wajib, gambar, max 2MB
            'notes' => 'nullable|string|max:500',
        ];
    }

    // Metode mount untuk menginisialisasi invoice
    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    // Metode untuk menyimpan bukti pembayaran
    public function savePayment()
    {
        $this->validate();

        // Simpan file ke storage
        $proofPath = $this->proofOfPayment->store('proofs-of-payment', 'public');

        // Buat entri pembayaran baru
        Payment::create([
            'invoice_id' => $this->invoice->id,
            'proof_of_payment_path' => $proofPath,
            'notes' => $this->notes,
            'uploaded_at' => now(), // Waktu unggah
            'status' => 'pending_verification', // Status awal
        ]);

        if ($this->invoice->status !== InvoiceStatus::PENDING_PAYMENT_VERIFICATION) {
            $this->invoice->status = InvoiceStatus::PENDING_PAYMENT_VERIFICATION;
            $this->invoice->save();
        }

        // Beri tahu pengguna bahwa pembayaran berhasil diunggah
        session()->flash('message', 'Bukti pembayaran Anda berhasil diunggah dan sedang menunggu verifikasi admin. Kami akan segera mengonfirmasi pembayaran Anda.');

        // Redirect atau emit event setelah berhasil
        return redirect()->route('occupant.dashboard'); // Sesuaikan dengan route detail pembayaran Anda
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.upload-payment-proof');
    }
}
