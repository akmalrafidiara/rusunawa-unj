<?php

namespace App\Livewire\Contracts\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount()
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications()
    {
        $pic = Auth::guard('contract')->user()->pic;
        if ($pic) {
            $this->notifications = $pic->unreadNotifications()->latest()->get();
            $this->unreadCount = $this->notifications->count();
        } else {
            $this->notifications = collect();
            $this->unreadCount = 0;
        }
    }

    public function markAsRead($notificationId)
    {
        $pic = Auth::guard('contract')->user()->pic;
        $notification = $pic?->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    public function markAllAsRead()
    {
        $pic = Auth::guard('contract')->user()->pic;
        $pic?->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function readAndRedirect($notificationId, $url)
    {
        $pic = Auth::guard('contract')->user()->pic;
        $notification = $pic?->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
        return $this->redirect($url, navigate: true);
    }

    public function render()
    {
        $this->refreshNotifications();
        return view('livewire.contracts.dashboard.notifications');
    }
}