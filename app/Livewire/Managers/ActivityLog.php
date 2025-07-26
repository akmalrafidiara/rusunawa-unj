<?php

namespace App\Livewire\Managers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog as ActivityLogModel;

class ActivityLog extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $loggable_type = '';
    public $loggable_id = '';
    public $orderBy = 'created_at';
    public $sort = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15],
        'loggable_type' => ['except' => ''],
        'loggable_id' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function updating($name)
    {
        if (in_array($name, ['search', 'loggable_type', 'loggable_id', 'orderBy', 'sort', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $logs = ActivityLogModel::with('loggable')
            ->when($this->loggable_type, function ($query) {
                $query->where('loggable_type', $this->loggable_type);
            })
            ->when($this->loggable_id, function ($query) {
                $query->where('loggable_id', $this->loggable_id);
            })
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        return view('livewire.managers.oprations.activity-logs.index', compact('logs'));
    }
}
