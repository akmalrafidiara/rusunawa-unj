<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Occupant extends Model implements Authenticatable
{
    use HasFactory, AuthenticatableTrait;

    protected $fillable = [
        'contract_id',
        'is_pic',
        'full_name',
        'email',
        'whatsapp_number',
        'identity_card_file',
        'community_card_file',
        'is_student',
        'student_id',
        'faculty',
        'study_program',
        'class_year',
        'agree_to_regulations',
        'notes',
        'status',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'agree_to_regulations' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function contracts()
    {
        return $this->belongsToMany(Contract::class, 'contract_occupant');
    }

    /**
     * Mendapatkan semua log verifikasi untuk penghuni ini.
     */
    public function verificationLogs()
    {
        return $this->morphMany(VerificationLog::class, 'loggable');
    }
}
