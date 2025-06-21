<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCluster extends Model
{
    protected $fillable = [
        'name',
        'address',
        'image',
        'description',
        'staff_id',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function allowedOccupantTypes()
    {
        return $this->belongsToMany(OccupantType::class, 'occupant_type_unit_cluster');
    }
}
