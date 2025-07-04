<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'status',
        'category',
    ];

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($announcement) {
            $announcement->slug = $announcement->generateUniqueSlug($announcement->title);
        });

        static::updating(function ($announcement) {
            if ($announcement->isDirty('title')) {
                $announcement->slug = $announcement->generateUniqueSlug($announcement->title);
            }
        });
    }

    protected function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'status' => AnnouncementStatus::class,
        'category' => AnnouncementCategory::class,
    ];
}