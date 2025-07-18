<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * App\Models\Invoice
 *
 * @property-read Contract $contract
 *
 * @property int $id
 * @property string $invoice_number
 * @property int $contract_id
 * @property string $description
 * @property float $amount
 * @property \Illuminate\Support\Carbon $due_at
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \App\Enums\InvoiceStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'contract_id',
        'description',
        'amount',
        'due_at',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'status' => InvoiceStatus::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $today = Carbon::today();
        $datePart = $today->format('dmy');
        $invoiceCountToday = self::whereDate('created_at', $today)->count();
        $sequenceNumber = $invoiceCountToday + 1;
        $sequencePart = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        $newInvoiceNumber = 'INV' . $datePart . $sequencePart;
        if (self::where('invoice_number', $newInvoiceNumber)->exists()) {
             $sequencePart = str_pad($sequenceNumber + 1, 4, '0', STR_PAD_LEFT);
             return 'INV' . $datePart . $sequencePart;
        }

        return $newInvoiceNumber;
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function generatePdf()
    {
        $pdf = Pdf::loadView('exports.invoice', [
            'invoice' => $this,
            'contract' => $this->contract,
        ]);

        return $pdf->output();
    }
}
