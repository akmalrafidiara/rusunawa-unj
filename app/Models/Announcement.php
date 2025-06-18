<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementCategory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'category',
    ];

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    protected $casts = [
        'status' => AnnouncementStatus::class,
        'category' => AnnouncementCategory::class,
    ];
}