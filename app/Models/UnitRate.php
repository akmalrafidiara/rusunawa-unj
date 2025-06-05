<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitRate extends Model
{
    protected $table = 'rates';

    protected $fillable = [
        'amount',
        'type',
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'unit_rate', 'unit_rate_id', 'unit_id');
    }
}
