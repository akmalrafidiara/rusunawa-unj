<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    protected $table = 'unit_types';

    protected $fillable = [
        'name',
        'description',
        'image',
        'facilities',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];
}
