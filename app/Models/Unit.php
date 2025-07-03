<?php

namespace App\Models;

use App\Enums\GenderAllowed;
use App\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Unit extends Model
{
    protected $fillable = [
        'room_number',
        'capacity',
        'virtual_account_number',
        'gender_allowed',
        'status',
        'notes',
        'image',
        'unit_type_id',
        'unit_cluster_id',
    ];

    public function unitType()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function unitCluster()
    {
        return $this->belongsTo(UnitCluster::class);
    }

    public function scopeAvailableWithFilters(Builder $query, array $filters = []): Builder
    {
        $occupantTypeId = $filters['occupantTypeId'] ?? null;
        $genderAllowed = $filters['genderAllowed'] ?? null;
        $unitClusterId = $filters['unitClusterId'] ?? null;

        $query->where('status', 'available');

        if ($genderAllowed && $genderAllowed !== 'general') {
            $query->whereIn('gender_allowed', ['general', $genderAllowed]);
        }

        if ($occupantTypeId) {
            $occupantType = OccupantType::find($occupantTypeId);
            if ($occupantType) {
                $accessibleClusterIds = $occupantType->accessibleClusters()->pluck('id')->toArray();

                if (!empty($accessibleClusterIds)) {
                    $query->whereIn('unit_cluster_id', $accessibleClusterIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($unitClusterId) {
            $query->where('unit_cluster_id', $unitClusterId);
        }

        return $query;
    }

    public function maintenanceSchedule()
    {
        return $this->hasOne(MaintenanceSchedule::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    protected $casts = [
        'gender_allowed' => GenderAllowed::class,
        'status' => UnitStatus::class,
    ];
}
