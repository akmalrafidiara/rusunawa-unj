<?php

namespace App\Livewire\Frontend\Announcement;

use App\Models\Announcement as AnnouncementModel;
use App\Enums\AnnouncementStatus;
use Livewire\Component;

class ShowAnnouncement extends Component
{
    public AnnouncementModel $announcement;
    public $relatedAnnouncements;
    public $slug;

    public function mount($slug) 
    {
        $this->slug = $slug;

        // Temukan pengumuman berdasarkan slug
        $this->announcement = AnnouncementModel::where('slug', $this->slug)
                                ->where('status', AnnouncementStatus::Published->value)
                                ->firstOrFail();

        $this->relatedAnnouncements = AnnouncementModel::query()
            ->where('status', AnnouncementStatus::Published->value)
            ->where('id', '!=', $this->announcement->id)
            ->latest()
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.frontend.announcement.announcement-detail.index');
    }
}