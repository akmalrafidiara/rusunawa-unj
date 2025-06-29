<?php

namespace App\Livewire\Frontend\Announcement;

use App\Models\Announcement as AnnouncementModel;
use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Announcement extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $categoryOptions;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->categoryOptions = AnnouncementCategory::options();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }


    public function render()
    {
        $announcements = AnnouncementModel::query()
            ->where('status', AnnouncementStatus::Published->value)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->latest()
            ->paginate(9);

        return view('livewire.frontend.announcement.announcement-list.index', [
            'announcements' => $announcements,
            'categoryOptions' => $this->categoryOptions,
        ]);
    }
}
