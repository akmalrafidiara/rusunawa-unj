<?php

namespace App\Livewire\Managers;

use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Jobs\SendRejectionOccupantEmail;
use App\Jobs\SendWelcomeEmail;
use App\Models\Invoice;
use App\Models\Occupant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class OccupantVerification extends Component
{
    use WithPagination;

    public $occupant;

    public $responseMessage;
    public $contractPrice;

    public $showModal = false;
    public $modalType = '';
    public $occupantIdBeingSelected;

    public $search = '';

    public function render()
    {
        $occupants = Occupant::query()
            ->where('status', OccupantStatus::PENDING_VERIFICATION)
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', "%{$this->search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$this->search}%")
                    ->orWhereHas('contracts', function ($contractQuery) {
                        $contractQuery->where('contract_code', 'like', "%{$this->search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.managers.responses.occupant-verification.index', compact('occupants'));
    }

    public function selectOccupant($occupantId)
    {
        $this->occupantIdBeingSelected = $occupantId;
        $this->occupant = Occupant::find($occupantId);
        $this->contractPrice = $this->occupant->contracts()->first()->total_price ?? null;
    }

    public function showResponseModal($responseType)
    {
        $this->showModal = true;
        $this->modalType = $responseType;

        if($responseType === 'accept') {
            $this->responseMessage = 'Data kamu berhasil diverifikasi. Silakan lanjutkan ke pembayaran sewa.';
        } elseif ($responseType === 'reject') {
            $this->responseMessage = 'Data kamu tidak memenuhi syarat. Silakan perbaiki data dan ajukan kembali.';
        }
    }

    public function acceptOccupant()
    {
        $this->occupant->status = OccupantStatus::VERIFIED;
        $this->occupant->save();

        $this->occupant->contracts()->first()->update([
            'total_price' => $this->contractPrice,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'contract_id' => $this->occupant->contracts()->first()->id,
            'description' => 'Pembayaran sewa pertama untuk unit ' . $this->occupant->contracts()->first()->unit->room_number,
            'amount' => $this->occupant->contracts()->first()->total_price,
            'due_at' => Carbon::now()->addHours(config('tenancy.initial_payment_due_hours')),
                    'status' => InvoiceStatus::UNPAID,
        ]);

        // Log the verification
        $this->occupant->verificationLogs()->create([
            'processed_by' => auth('web')->id(),
            'status' => 'approved',
            'reason' => $this->responseMessage,
        ]);

        // Create a signed URL for occupant login
        $authUrl = URL::temporarySignedRoute(
            'occupant.auth.url',
            now()->addHours(value: 1),
            ['data' => encrypt($this->occupant->contracts()->first()->id)]
        );

        LivewireAlert::title('Respon berhasil di kirim')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Send welcome email to the occupant
        SendWelcomeEmail::dispatch($this->occupant, $this->occupant->contracts()->first(), $authUrl, $invoice);

        $this->resetProperties();
    }

    public function rejectOccupant()
    {
        $this->occupant->status = OccupantStatus::REJECTED;
        $this->occupant->save();

        // Log the verification
        $this->occupant->verificationLogs()->create([
            'processed_by' => auth('web')->id(),
            'status' => 'rejected',
            'reason' => $this->responseMessage,
        ]);

        LivewireAlert::title('Respon berhasil di kirim')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Send rejection email to the occupant
        SendRejectionOccupantEmail::dispatch($this->occupant, $this->responseMessage);

        $this->resetProperties();
    }

    public function resetProperties()
    {
        $this->occupant = null;
        $this->responseMessage = null;
        $this->contractPrice = null;
        $this->showModal = false;
        $this->modalType = '';
        $this->occupantIdBeingSelected = null;
        $this->search = '';
    }
}
