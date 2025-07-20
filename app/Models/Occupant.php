<?php

namespace App\Models;

use App\Enums\GenderAllowed;
use App\Enums\OccupantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupant extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'whatsapp_number',
        'gender',
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
        'gender' => GenderAllowed::class,
        'is_student' => 'boolean',
        'agree_to_regulations' => 'boolean',
        'status' => OccupantStatus::class,
    ];

    public function contracts()
    {
        return $this->belongsToMany(Contract::class, 'contract_occupant');
    }

    public function picContracts()
    {
        return $this->hasMany(Contract::class, 'contract_pic');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Mendapatkan semua log verifikasi untuk penghuni ini.
     */
    public function verificationLogs()
    {
        return $this->morphMany(VerificationLog::class, 'loggable');
    }
}
