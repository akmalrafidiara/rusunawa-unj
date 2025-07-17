<?php

namespace App\Livewire\Managers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount;

    protected $listeners = ['notification-read' => 'refreshNotifications'];

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $user = Auth::user();
        $this->notifications = $user->unreadNotifications()->latest()->get();
        $this->unreadCount = $this->notifications->count();
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function readAndRedirect($notificationId, $url)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
        return $this->redirect($url, navigate: true);
    }

    public function render()
    {
        $this->refreshNotifications();
        return view('livewire.managers.notifications.index');
    }
}