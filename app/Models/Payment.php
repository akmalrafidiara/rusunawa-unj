<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'verified_by',
        'proof_of_payment_path',
        'notes',
        'uploaded_at',
        'verified_at',
        'status',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'status' => PaymentStatus::class,
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Mendapatkan semua log verifikasi untuk pembayaran ini.
     */
    public function verificationLogs()
    {
        return $this->morphMany(VerificationLog::class, 'loggable');
    }
}