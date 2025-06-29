<?php

namespace App\Livewire\Frontend\Announcement;

use App\Models\Announcement as AnnouncementModel;
use App\Enums\AnnouncementStatus;
use Livewire\Component;

class ShowAnnouncement extends Component
{
    public AnnouncementModel $announcement;

    public $relatedAnnouncements;

    public function mount(AnnouncementModel $announcement)
    {
        $this->announcement = $announcement;
        $this->relatedAnnouncements = AnnouncementModel::query()
            ->where('status', AnnouncementStatus::Published->value)
            ->where('id', '!=', $announcement->id)
            ->latest()
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.announcement.announcement-detail.index')->layout('components.layouts.frontend');
    }
}
