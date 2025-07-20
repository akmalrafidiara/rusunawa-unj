<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'processed_by',
        'status',
        'reason',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'status' => VerificationStatus::class,
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function processor() // Definisi relasi 'processor'
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}