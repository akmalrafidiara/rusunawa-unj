<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractNotification extends Notification
{
    use Queueable;

    public Contract $contract;
    public string $message;
    public string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contract $contract, $message, $type = 'expiring')
    {
        $this->contract = $contract;
        $this->message = $message;
        $this->type = $type; // expiring, expired, renewed
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
            'contract_id' => $this->contract->id,
            'contract_code' => $this->contract->contract_code,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => 'document-text',
            'color' => $this->getColorByType(),
            'url' => route('contracts'),
        ];
    }

    private function getColorByType()
    {
        return match($this->type) {
            'expiring' => 'orange',
            'expired' => 'red',
            'renewed' => 'green',
            default => 'blue',
        };
    }
}
