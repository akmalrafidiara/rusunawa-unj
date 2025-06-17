<?php

namespace App\Models;

use App\Enums\GenderAllowed;
use App\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;

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

    public function rates()
    {
        return $this->belongsToMany(UnitRate::class, 'unit_rate', 'unit_id', 'rate_id');
    }

    protected $casts = [
        'gender_allowed' => GenderAllowed::class,
        'status' => UnitStatus::class,
    ];
}
