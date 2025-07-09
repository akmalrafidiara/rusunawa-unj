<?php

namespace App\Models;

use App\Enums\MaintenanceRecordStatus;
use App\Enums\MaintenanceRecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'maintenance_schedule_id',
        'type',
        'scheduled_date',
        'completion_date',
        'status',
        'notes',
        'is_late',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_date' => 'date',
        'type' => MaintenanceRecordType::class,
        'status' => MaintenanceRecordStatus::class,
        'is_late' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    /**
     * Get all of the maintenance record's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}