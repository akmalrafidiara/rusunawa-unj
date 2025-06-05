<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCluster extends Model
{
    protected $fillable = [
        'name',
        'address',
        'staff_id',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
