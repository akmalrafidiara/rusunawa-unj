<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MaintenanceSchedule;

class MaintenanceReminderNotification extends Notification
{
    use Queueable;

    protected $schedule;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param MaintenanceSchedule $schedule
     * @param string $message
     */
    public function __construct(MaintenanceSchedule $schedule, string $message)
    {
        $this->schedule = $schedule;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Kita akan menyimpan notifikasi di database
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'maintenance_schedule_id' => $this->schedule->id,
            'unit_room_number' => $this->schedule->unit->room_number,
            'message' => $this->message,
            'url' => route('maintenance') . '?selectedScheduleId=' . $this->schedule->id,
        ];
    }
}