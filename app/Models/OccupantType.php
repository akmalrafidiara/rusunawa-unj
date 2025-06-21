<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccupantType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'requires_verification',
    ];

    protected $casts = [
        'requires_verification' => 'boolean',
    ];

    public function accessibleClusters()
    {
        return $this->belongsToMany(UnitCluster::class, 'occupant_type_unit_cluster');
    }
}
