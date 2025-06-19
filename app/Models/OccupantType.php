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
}
