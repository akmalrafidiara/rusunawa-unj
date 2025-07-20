<?php

namespace App\Livewire\Managers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount = 0;
    public $isOpen = false;

    protected $listeners = [
        'notification-created' => 'refreshNotifications',
        'notification-read' => 'refreshNotifications'
    ];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $user = Auth::user();
        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->refreshNotifications();
    }

    public function readAndRedirect($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            if ($notification->unread()) {
                $notification->markAsRead();
            }

            $url = $notification->data['url'] ?? '#';
            $this->redirectRoute($url);
        }
    }

    public function getNotificationIcon($type)
    {
        return match($type) {
            'user-check' => 'user-check',
            'currency-dollar' => 'currency-dollar',
            'document-text' => 'document-text',
            'exclamation-triangle' => 'exclamation-triangle',
            'wrench-screwdriver' => 'wrench-screwdriver',
            default => 'bell'
        };
    }

    public function getNotificationColor($color)
    {
        return match($color) {
            'red' => 'bg-red-500',
            'yellow' => 'bg-yellow-500',
            'green' => 'bg-green-500',
            'blue' => 'bg-blue-500',
            'orange' => 'bg-orange-500',
            'purple' => 'bg-purple-500',
            default => 'bg-gray-500'
        };
    }

    public function render()
    {
        return view('livewire.managers.notification-dropdown');
    }
}
