<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReportLog extends Model
{
    use HasFactory;

    protected $table = 'reports_log';

    protected $fillable = [
        'report_id',
        'user_id',
        'action_by_role',
        'old_status',
        'new_status',
        'notes',
    ];

    protected $casts = [
        'old_status' => ReportStatus::class,
        'new_status' => ReportStatus::class,
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Add this new relationship for attachments
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}