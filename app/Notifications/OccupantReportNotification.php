<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OccupantReportNotification extends Notification
{
    use Queueable;

    public $report;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report, $message)
    {
        $this->report = $report;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'message' => $this->message,
            'url' => route('complaint.ongoing-detail', ['unique_id' => $this->report->unique_id]),
        ];
    }
}