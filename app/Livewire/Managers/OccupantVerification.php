<?php

namespace App\Livewire\Managers;

use App\Enums\ContractStatus; // Tambahkan ini jika belum ada
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Enums\VerificationStatus;
use App\Jobs\SendOccupantVerificationEmail;
use App\Jobs\SendRejectionOccupantEmail;
use App\Jobs\SendWelcomeEmail;
use App\Models\Invoice;
use App\Models\Occupant;
use App\Models\Contract;
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
    public $contract;
    public $isPic;
    public $contractPrice;
    public $latestInvoice;
    public $responseMessage;

    public bool $showModal = false;
    public string $modalType = '';
    public $tab = 'recent'; // Default tab

    public string $search = '';
    public string $occupantVerificationType = '';

    public $occupantIdBeingSelected = null; // ID penghuni yang sedang dipilih

    public function render()
    {
        $contracts = collect(); // Inisialisasi koleksi kosong
        $historyLogs = collect();     // Inisialisasi koleksi kosong
        $paginator = null; // Inisialisasi paginator default

        if ($this->tab === 'recent') {
            $contracts = Contract::query()
                ->whereNotIn('status', [ContractStatus::CANCELLED, ContractStatus::EXPIRED])
                ->whereHas('occupants', function ($query) {
                    $query->where('occupants.status', OccupantStatus::PENDING_VERIFICATION);
                })
                ->with(['occupants' => function ($query) {
                    $query->where('occupants.status', OccupantStatus::PENDING_VERIFICATION)
                        ->orderBy('occupants.updated_at', 'asc');
                }])
                ->when($this->search, function ($query) {
                    $query->where('contract_code', 'like', "%{$this->search}%")
                        ->orWhereHas('occupants', function ($occupantQuery) {
                            $occupantQuery->where('full_name', 'like', "%{$this->search}%")
                                        ->orWhere('whatsapp_number', 'like', "%{$this->search}%");
                        });
                })
                ->withMin(['occupants as earliest_pending_occupant_update' => function ($query) {
                    $query->where('occupants.status', OccupantStatus::PENDING_VERIFICATION);
                }], 'updated_at')
                ->orderBy('earliest_pending_occupant_update', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(10, pageName: 'recentPage');

            $paginator = $contracts;

        } elseif ($this->tab === 'history') {
            $historyLogs = VerificationLog::query()
                ->where('loggable_type', Occupant::class)
                ->with('loggable', 'processor')
                ->orderBy('processed_at', 'desc')
                ->paginate(10, pageName: 'historyPage'); // Gunakan pageName berbeda untuk paginasi
            $paginator = $historyLogs;
        }

        return view('livewire.managers.responses.occupant-verification.index', [
            'contracts' => $contracts,
            'historyLogs' => $historyLogs,
            'paginator' => $paginator,
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

    public function selectOccupantOnly($occupantId)
    {
        $this->occupantIdBeingSelected = $occupantId;

        $occupant = Occupant::with('verificationLogs')->find($occupantId);

        if (!$occupant) {
            $this->occupantVerificationType = 'Penghuni tidak ditemukan.';
            $this->contractPrice = null;
            $this->occupant = null;
            return;
        }

        $this->occupant = $occupant;

        // Cek apakah penghuni ini adalah PIC
        $this->isPic = $occupant->picContracts()->first() !== null;

        // Ambil kontrak terkait dengan penghuni ini
        $this->contract = $occupant->contracts()->with(['pic', 'invoices'])->first();

        if (!$this->contract) {
            $this->occupantVerificationType = 'Kontrak tidak ditemukan untuk penghuni ini.';
            $this->contractPrice = null;
            return;
        }

        // Ambil invoice terakhir untuk kontrak ini
        $this->latestInvoice = $this->contract->invoices->last();

        // Tentukan jenis verifikasi berdasarkan status dan apakah penghuni adalah PIC
        if ($this->isPic) {
            if (!$this->latestInvoice && !$occupant->verificationLogs()->where('status', VerificationStatus::APPROVED)->exists()) {
                $this->occupantVerificationType = 'Pengajuan Kontrak Baru (PIC)';
            } else {
                $this->occupantVerificationType = 'Perubahan Data / Re-verifikasi (PIC)';
            }
            $this->contractPrice = $this->contract->total_price ?? null;
        } else { // Bukan PIC
            if (!$occupant->verificationLogs()->where('status', VerificationStatus::APPROVED)->exists()) {
                $this->occupantVerificationType = 'Penambahan Penghuni Baru (Non-PIC)';
            } else {
                $this->occupantVerificationType = 'Perubahan Data / Re-verifikasi (Non-PIC)';
            }
        }
    }

    public function selectOccupant($occupantId, $contractId)
    {
        $this->occupantIdBeingSelected = $occupantId;

        $occupant = Occupant::with('verificationLogs')->find($occupantId);

        if (!$occupant) {
            $this->occupantVerificationType = 'Penghuni tidak ditemukan.';
            $this->contractPrice = null;
            $this->occupant = null;
            return;
        }

        $contract = Contract::where('id', $contractId)
                            ->whereHas('occupants', function ($query) use ($occupantId) {
                                $query->where('occupants.id', $occupantId);
                            })
                            ->with(['pic', 'invoices'])
                            ->first();

        if (!$contract) {
            $this->occupantVerificationType = 'Kontrak tidak ditemukan atau Penghuni tidak terkait dengan Kontrak ini.';
            $this->contractPrice = null;
            $this->occupant = null;
            return;
        }

        $this->occupant = $occupant;

        $this->contract = $contract;

        $this->isPic = $contract->pic && $contract->pic->id === $this->occupant->id;

        $this->latestInvoice = $contract->invoices->last();


        $wasPreviouslyApproved = $this->occupant->verificationLogs
        ->where('status', 'approved')
        ->isNotEmpty();

        if ($this->isPic) {
            $this->contractPrice = $contract->total_price ?? null;

            if (!$this->latestInvoice && !$wasPreviouslyApproved) {
                $this->occupantVerificationType = 'Pengajuan Kontrak Baru (PIC)';
            } else {
                $this->occupantVerificationType = 'Perubahan Data / Re-verifikasi (PIC)';
            }
        } else { // Bukan PIC
            if (!$wasPreviouslyApproved) {
                $this->occupantVerificationType = 'Penambahan Penghuni Baru (Non-PIC)';
            } else {
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
        if (!$this->contract) {
            LivewireAlert::error()->title('Kontrak tidak ditemukan untuk penghuni ini.')->toast()->position('top-end')->show();
            return;
        }

        $this->occupant->status = OccupantStatus::VERIFIED;
        $this->occupant->save();

        $generatedInvoice = null;
        if ($this->isPic && !$this->latestInvoice) {

            if ($this->contractPrice !== null) {
                $this->contract->update([
                    'total_price' => $this->contractPrice,
                ]);
            }

            $generatedInvoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'contract_id' => $this->contract->id,
                'description' => 'Pembayaran sewa pertama untuk unit ' . $this->contract->unit->room_number,
                'amount' => $this->contract->total_price, // Use contract's total_price (updated above)
                'due_at' => Carbon::now()->addHours(config('tenancy.initial_payment_due_hours')),
                'status' => InvoiceStatus::UNPAID,
            ]);

            $authUrl = URL::temporarySignedRoute(
                'contract.auth.url',
                now()->addHours(value: 1),
                ['data' => encrypt($this->contract->id)]
            );

            SendWelcomeEmail::dispatch($this->occupant, $this->contract, $authUrl, $generatedInvoice);

            LivewireAlert::info()
                ->title('PIC berhasil diverifikasi. Invoice pertama berhasil dibuat.')
                ->toast()
                ->position('top-end')
                ->show();
        } else {
            if ($this->isPic && $this->latestInvoice) {
                LivewireAlert::info()
                    ->title('PIC berhasil diverifikasi. Invoice pertama telah dibuat sebelumnya.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            } else if (!$this->isPic) {
                LivewireAlert::info()
                    ->title('Penghuni tambahan berhasil diverifikasi. Tidak ada invoice baru yang dibuat karena bukan PIC.')
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
        }

        $this->occupant->verificationLogs()->create([
            'processed_by' => auth('web')->id(),
            'status' => VerificationStatus::APPROVED,
            'reason' => $this->responseMessage,
        ]);

        SendOccupantVerificationEmail::dispatch($this->occupant, $this->contract, $this->occupant->verificationLogs->last());

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
            'status' => VerificationStatus::REJECTED,
            'reason' => $this->responseMessage,
        ]);

        LivewireAlert::title('Respon berhasil dikirim')
        ->success()
        ->toast()
        ->position('top-end')
        ->show();

        // Send rejection email to the occupant
        SendOccupantVerificationEmail::dispatch($this->occupant, $this->contract, $this->occupant->verificationLogs->last());

        $this->resetProperties();
    }

    public function resetProperties()
    {
        $this->occupant = null;
        $this->contract = null;
        $this->isPic = false;
        $this->responseMessage = null;
        $this->contractPrice = null;
        $this->showModal = false;
        $this->modalType = '';
        $this->search = '';
        $this->occupantVerificationType = '';
        $this->occupantIdBeingSelected = null;
    }
}