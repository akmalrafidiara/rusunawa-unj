<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'path',
        'attachable_id',
        'attachable_type',
    ];

    /**
     * Get the parent attachable model (polymorphic relation).
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
