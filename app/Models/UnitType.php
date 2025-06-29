<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    protected $table = 'unit_types';

    protected $fillable = [
        'name',
        'description',
        'facilities',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function unitPrices()
    {
        return $this->hasMany(UnitPrice::class);
    }

    public function availableUnitsCount($unitClusterIds = null)
    {
        if (is_null($unitClusterIds)) {
            return $this->units()
                ->where('status', 'available')
                ->count();
        
            }  
        return $this->units()
            ->where('status', 'available')
            ->whereIn('unit_cluster_id', $unitClusterIds)
            ->count();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
