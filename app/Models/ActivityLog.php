<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'activity',
        'url',
        'ip_address',
        'user_agent',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }
}
