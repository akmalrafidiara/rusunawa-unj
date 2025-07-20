<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationNotification extends Notification
{
    use Queueable;

    public $invoice;
    public $message;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, $message, $type = 'pending')
    {
        $this->invoice = $invoice;
        $this->message = $message;
        $this->type = $type; // pending, confirmed, rejected
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
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->amount,
            'message' => $this->message,
            'type' => $this->type,
            'icon' => 'currency-dollar',
            'color' => $this->getColorByType(),
            'url' => route('payment.confirmation'),
        ];
    }

    private function getColorByType()
    {
        return match($this->type) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'rejected' => 'red',
            default => 'blue'
        };
    }
}
