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
        'facilities' => 'array', // Ini yang sangat penting!
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function rates()
    {
        return $this->belongsToMany(UnitRate::class, 'unit_type_rate', 'unit_type_id', 'rate_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
