<?php

namespace App\Livewire\Contracts\Dashboard;

use App\Models\Announcement;
use Livewire\Component;

class Announcements extends Component
{
    public $announcements;

    public function mount()
    {
        $this->announcements = Announcement::where('status', 'published')
            ->latest()
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.announcements');
    }
}
