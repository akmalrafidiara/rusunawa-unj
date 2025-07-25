<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulation extends Model
{
    protected $table = 'regulations';

    protected $fillable = [
        'title',
        'content',
        'priority',
    ];
}
