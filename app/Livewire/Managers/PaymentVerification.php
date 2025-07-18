<?php

namespace App\Livewire\Managers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User; // Assuming 'User' model for 'verified_by' relationship
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentVerification extends Component
{
    use WithPagination;

    public $payment;

    public $responseMessage;
    public $showModal = false;
    public $modalType = '';
    public $paymentIdBeingSelected;

    public $search = '';

    protected $listeners = ['refreshPaymentVerification' => '$refresh'];

    public function render()
    {
        $payments = Payment::query()
            ->where('status', PaymentStatus::PENDING_VERIFICATION)
            ->when($this->search, function ($query) {
                $query->whereHas('invoice.contract.occupants', function ($occupantQuery) {
                    $occupantQuery->where('full_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('invoice', function ($invoiceQuery) {
                    $invoiceQuery->where('invoice_number', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('uploaded_at', 'desc')
            ->paginate(10);

        return view('livewire.managers.responses.payment-verification.index', compact('payments'));
    }

    public function selectPayment($paymentId)
    {
        $this->paymentIdBeingSelected = $paymentId;
        $this->payment = Payment::with(['invoice.contract.unit', 'invoice.contract.occupants'])->find($paymentId);
        $this->resetValidation();
    }

    public function showResponseModal($responseType)
    {
        if (!$this->payment) {
            LivewireAlert::title('Pilih pembayaran terlebih dahulu.')
            ->warning()
            ->toast()
            ->position('top-end')
            ->show();
            return;
        }
        $this->showModal = true;
        $this->modalType = $responseType;

        if ($responseType === 'accept') {
            $this->responseMessage = 'Pembayaran berhasil diverifikasi. Invoice telah dilunasi.';
        } elseif ($responseType === 'reject') {
            $this->responseMessage = 'Bukti pembayaran tidak valid. Mohon unggah ulang bukti pembayaran yang benar.';
        }
    }

    public function acceptPayment()
    {
        $this->validate([
            'responseMessage' => 'required|string|max:500',
        ]);

        if (!$this->payment) {
            LivewireAlert::title('Terjadi kesalahan. Pembayaran tidak ditemukan.')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            $this->resetProperties();
            return;
        }

        $this->payment->status = PaymentStatus::APPROVED;
        $this->payment->verified_by = auth('web')->id();
        $this->payment->verified_at = now();
        $this->payment->save();

        // Update associated invoice status to PAID
        if ($this->payment->invoice) {
            $this->payment->invoice->status = InvoiceStatus::PAID;
            $this->payment->invoice->paid_at = now();
            $this->payment->invoice->save();
        }

        // Update contract status to active if it exists
        if ($this->payment->invoice && $this->payment->invoice->contract) {
            $this->payment->invoice->contract->status = \App\Enums\ContractStatus::ACTIVE;
            $this->payment->invoice->contract->save();
        }

        // Log the verification
        $this->payment->verificationLogs()->create([
            'processed_by' => auth('web')->id(),
            'status' => PaymentStatus::APPROVED,
            'reason' => $this->responseMessage,
        ]);

        LivewireAlert::title('Verifikasi pembayaran berhasil diterima.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Optionally send a notification/email to the occupant
        // SendPaymentAcceptedEmail::dispatch($this->payment);

        $this->resetProperties();
        $this->dispatch('refreshPaymentVerification');
    }

    public function rejectPayment()
    {
        $this->validate([
            'responseMessage' => 'required|string|max:500',
        ]);

        if (!$this->payment) {
            LivewireAlert::title('Terjadi kesalahan. Pembayaran tidak ditemukan.')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
            $this->resetProperties();
            return;
        }

        $this->payment->status = PaymentStatus::REJECTED;
        $this->payment->verified_by = auth('web')->id(); // Assign current logged-in user as verifier
        $this->payment->verified_at = now();
        $this->payment->save();

        if ($this->payment->invoice) {
            $this->payment->invoice->status = InvoiceStatus::UNPAID;
            $this->payment->invoice->save();
        }

        // Log the verification
        $this->payment->verificationLogs()->create([
            'processed_by' => auth('web')->id(),
            'status' => PaymentStatus::REJECTED,
            'reason' => $this->responseMessage,
        ]);
        LivewireAlert::title('Verifikasi pembayaran berhasil ditolak.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Optionally send a notification/email to the occupant explaining rejection
        // SendPaymentRejectedEmail::dispatch($this->payment, $this->responseMessage);

        $this->resetProperties();
        $this->dispatch('refreshPaymentVerification');
    }

    public function resetProperties()
    {
        $this->payment = null;
        $this->responseMessage = null;
        $this->showModal = false;
        $this->modalType = '';
        $this->paymentIdBeingSelected = null;
        $this->search = '';
    }
}
