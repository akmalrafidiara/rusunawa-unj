<?php

namespace App\Models;

use App\Enums\EmergencyContactRole;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'role',
        'phone',
        'address',
    ];

    protected $casts = [
        'role' => EmergencyContactRole::class,
    ];
}
