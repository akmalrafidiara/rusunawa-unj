<?php

namespace App\Models;

use App\Enums\MaintenanceScheduleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'frequency_months',
        'next_due_date',
        'last_completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'last_completed_at' => 'datetime',
        'status' => MaintenanceScheduleStatus::class,
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}