<?php

namespace App\Livewire\Managers;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread, read
    public $selectedNotifications = [];
    public $selectAll = false;

    protected $listeners = ['notification-read' => 'refreshNotifications'];

    public function refreshNotifications()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $query->update(['read_at' => now()]);
        $this->dispatch('notification-read');
    }

    public function markSelectedAsRead()
    {
        if (!empty($this->selectedNotifications)) {
            $user = Auth::user();
            $user->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->update(['read_at' => now()]);

            $this->selectedNotifications = [];
            $this->selectAll = false;
            $this->dispatch('notification-read');
        }
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedNotifications)) {
            $user = Auth::user();
            $user->notifications()
                ->whereIn('id', $this->selectedNotifications)
                ->delete();

            $this->selectedNotifications = [];
            $this->selectAll = false;
            $this->dispatch('notification-read');
        }
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedNotifications = $this->getNotifications()->pluck('id')->toArray();
        } else {
            $this->selectedNotifications = [];
        }
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

    public function getNotifications()
    {
        $user = Auth::user();
        $query = $user->notifications()->latest();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->paginate(20);
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
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();

        return view('livewire.managers.notifications.index', [
            'notifications' => $this->getNotifications(),
            'unreadCount' => $unreadCount
        ]);
    }
}
