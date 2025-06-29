<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestQuestion extends Model
{
    protected $table = 'guest_questions';

    protected $fillable = [
        'fullName',
        'formPhoneNumber',
        'formEmail',
        'message',
        'is_read',
    ];

    protected $attributes = [
        'is_read' => false,
    ];
}