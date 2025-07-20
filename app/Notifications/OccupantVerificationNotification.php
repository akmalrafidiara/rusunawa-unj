<?php

namespace App\Notifications;

use App\Models\Occupant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OccupantVerificationNotification extends Notification
{
    use Queueable;

    public $occupant;
    public $message;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Occupant $occupant, $message, $type = 'pending')
    {
        $this->occupant = $occupant;
        $this->message = $message;
        $this->type = $type; // pending, verified, rejected
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'occupant_id' => $this->occupant->id,
            'occupant_name' => $this->occupant->full_name,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => 'user-check',
            'color' => $this->getColorByType(),
            'url' => route('occupant.verification'),
        ];
    }

    private function getColorByType()
    {
        return match($this->type) {
            'pending' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            default => 'blue'
        };
    }
}