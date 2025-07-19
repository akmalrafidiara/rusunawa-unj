<?php

namespace App\Livewire\Managers;

use App\Enums\ContractStatus; // Tambahkan ini jika belum ada
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Jobs\SendRejectionOccupantEmail;
use App\Jobs\SendWelcomeEmail;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\VerificationLog; // Import model VerificationLog
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
// Hapus 'use Livewire\Attributes\Computed;' jika sebelumnya ada

class OccupantVerification extends Component
{
    use WithPagination;

    public $occupant;
    public $responseMessage;
    public $contractPrice;

    public bool $showModal = false;
    public string $modalType = '';
    public $occupantIdBeingSelected;
    public $tab = 'recent'; // Default tab

    public string $search = '';
    public string $occupantVerificationType = '';

    public function render()
    {
        $recentOccupants = collect(); // Inisialisasi koleksi kosong
        $historyLogs = collect();     // Inisialisasi koleksi kosong
        $paginator = null; // Inisialisasi paginator default

        if ($this->tab === 'recent') {
            $recentOccupants = Occupant::query()
                ->where('status', OccupantStatus::PENDING_VERIFICATION)
                ->whereHas('contracts', function ($contractQuery) {
                    $contractQuery
                        ->where('status', '!=', ContractStatus::CANCELLED)
                        ->where('status', '!=', ContractStatus::EXPIRED);
                })
                ->when($this->search, function ($query) {
                    $query->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('whatsapp_number', 'like', "%{$this->search}%")
                        ->orWhereHas('contracts', function ($contractQuery) {
                            $contractQuery->where('contract_code', 'like', "%{$this->search}%");
                        });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, pageName: 'recentPage'); // Gunakan pageName berbeda untuk paginasi
            $paginator = $recentOccupants;

        } elseif ($this->tab === 'history') {
            $historyLogs = VerificationLog::query()
                ->where('loggable_type', Occupant::class)
                ->with('loggable', 'processor')
                ->orderBy('processed_at', 'desc')
                ->paginate(10, pageName: 'historyPage'); // Gunakan pageName berbeda untuk paginasi
            $paginator = $historyLogs;
        }

        return view('livewire.managers.responses.occupant-verification.index', [
            'recentOccupants' => $recentOccupants,
            'historyLogs' => $historyLogs,
            'paginator' => $paginator, // Kirim paginator yang sesuai ke view
        ]);
    }

    // Metode ini akan dipanggil saat search atau tab berubah untuk mereset paginasi
    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination untuk tab yang aktif
    }

    public function updatedTab()
    {
        $this->resetPage(); // Reset pagination saat tab berubah
        $this->reset(['occupant', 'occupantIdBeingSelected', 'responseMessage', 'contractPrice', 'occupantVerificationType']); // Hapus detail panel saat tab berubah
    }

    public function selectOccupant($occupantId)
    {
        $this->occupantIdBeingSelected = $occupantId;
        // Load the occupant and its relevant relationships for detailed checks
        $this->occupant = Occupant::with(['contracts.pic', 'contracts.invoices', 'verificationLogs'])
                                  ->find($occupantId);

        if (!$this->occupant) {
            $this->occupantVerificationType = 'Occupant Not Found';
            $this->contractPrice = null;
            return;
        }

        $contract = $this->occupant->contracts->first(); // Get the first contract associated with this occupant

        if (!$contract) {
            $this->occupantVerificationType = 'No Contract Found for this Occupant';
            $this->contractPrice = null;
            return;
        }

        $this->contractPrice = $contract->total_price ?? null;

        // Determine if this occupant is the Primary Contact (PIC) of this contract.
        $isPic = false;
        if ($contract->pic && $contract->pic->first()) { // Ensure pic relationship exists and is not empty
            $isPic = ($contract->pic->first()->id === $this->occupant->id);
        }

        // Check if this contract already has any invoices.
        $hasAnyInvoice = $contract->invoices->isNotEmpty();

        // Check if this specific occupant was previously approved (has an 'approved' verification log).
        $wasPreviouslyApproved = $this->occupant->verificationLogs
                                                ->where('status', 'approved')
                                                ->isNotEmpty();

        // Logic to set $this->occupantVerificationType based on the scenarios
        if ($isPic) {
            if (!$hasAnyInvoice && !$wasPreviouslyApproved) {
                // Scenario: This is the contract's PIC, and the contract has no prior invoices,
                // and the PIC themselves were not previously approved.
                // This indicates a completely new contract submission by the primary tenant.
                $this->occupantVerificationType = 'Pengajuan Kontrak Baru (PIC)';
            } else {
                // Scenario: This is the contract's PIC, but the contract already has invoices
                // or the PIC was previously approved (meaning they are editing their data).
                $this->occupantVerificationType = 'Perubahan Data / Re-verifikasi (PIC)';
            }
        } else { // Not PIC
            if (!$wasPreviouslyApproved) {
                // Scenario: This is a non-PIC occupant, and they were not previously approved.
                // This indicates a new additional occupant being added to an existing contract.
                $this->occupantVerificationType = 'Penambahan Penghuni Baru (Non-PIC)';
            } else {
                // Scenario: This is a non-PIC occupant, but they were previously approved.
                // This indicates an existing additional occupant is editing their data.
                $this->occupantVerificationType = 'Perubahan Data / Re-verifikasi (Non-PIC)';
            }
        }
    }

    public function showResponseModal($responseType)
    {
        $this->showModal = true;
        $this->modalType = $responseType;

        if($responseType === 'accept') {
            $this->responseMessage = 'Data kamu berhasil diverifikasi.';
        } elseif ($responseType === 'reject') {
            $this->responseMessage = 'Data kamu tidak memenuhi syarat. Silakan perbaiki data dan ajukan kembali.';
        }
    }

    public function acceptOccupant()
    {
        // Get the contract associated with the occupant being verified
        $contract = $this->occupant->contracts()->first();

        if (!$contract) {
            LivewireAlert::error()->title('Kontrak tidak ditemukan untuk penghuni ini.')->toast()->position('top-end')->show();
            return;
        }

        // Determine if the current occupant being verified is the Primary Contact (PIC) of this contract.
        $isPic = false;
        if ($contract->pic) {
            $isPic = (($contract->pic->first()->id ?? null) === ($this->occupant->id ?? null));
        }

        // Check if the contract already has any invoices.
        $hasAnyInvoice = $contract->invoices->isNotEmpty();

        // Update occupant status to VERIFIED
        $this->occupant->status = OccupantStatus::VERIFIED;
        $this->occupant->save();

        // Update the contract's total_price if provided.
        if ($this->contractPrice !== null) {
            $contract->update([
                'total_price' => $this->contractPrice,
            ]);
        }

        $generatedInvoice = null; // Initialize generated invoice to null

        // Generate invoice ONLY if this occupant is the PIC AND the contract does NOT have any existing invoices
        if ($isPic && !$hasAnyInvoice) {
            $generatedInvoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'contract_id' => $contract->id,
                'description' => 'Pembayaran sewa pertama untuk unit ' . $contract->unit->room_number,
                'amount' => $contract->total_price, // Use contract's total_price (updated above)
                'due_at' => Carbon::now()->addHours(config('tenancy.initial_payment_due_hours')),
                'status' => InvoiceStatus::UNPAID,
            ]);
            LivewireAlert::info()->title('Penghuni utama berhasil diverifikasi. Invoice pertama berhasil dibuat.')->toast()->position('top-end')->show();
        } else {
            // Inform the user why no invoice was generated
            if ($isPic && $hasAnyInvoice) {
                LivewireAlert::info()->title('Penghuni utama berhasil diverifikasi. Invoice pertama telah dibuat sebelumnya.')->toast()->position('top-end')->show();
            } else if (!$isPic) {
                LivewireAlert::info()->title('Penghuni tambahan berhasil diverifikasi. Tidak ada invoice baru yang dibuat karena bukan PIC.')->toast()->position('top-end')->show();
            }
        }

        // Log the verification
        $this->occupant->verificationLogs()->create([
            'processed_by' => auth('web')->id(), // Manager's ID
            'status' => 'approved',
            'reason' => $this->responseMessage,
        ]);

        // Create a signed URL for occupant login
        $authUrl = URL::temporarySignedRoute(
            'occupant.auth.url',
            now()->addHours(value: 1),
            ['data' => encrypt($contract->id)]
        );

        // Send welcome email to the occupant (passing null if no invoice was generated)
        SendWelcomeEmail::dispatch($this->occupant, $contract, $authUrl, $generatedInvoice);

        // Reset properties to clear the modal and selection
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

        LivewireAlert::title('Respon berhasil dikirim')
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
        $this->occupantVerificationType = '';
    }
}