<?php

namespace App\Livewire\Managers;

use App\Models\GuestQuestion as GuestQuestionModel;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\WithPagination;

class GuestQuestions extends Component
{
    use WithPagination;

    // Data properties
    public $search = '';
    public $readFilter = '';
    public $perPage = 10;
    public $orderBy = 'created_at';
    public $sort = 'desc';

    // Modal & confirmation properties
    public $showModal = false;
    public $modalType = ''; // 'detail'
    public $selectedQuestionId = null;

    // Query string
    protected $queryString = [
        'search' => ['except' => ''],
        'readFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'desc'],
    ];

    // Listeners for LivewireAlert
    protected $listeners = [
        'markAsReadConfirmed',
        'deleteQuestionConfirmed',
    ];

    /**
     * Reset pagination when filter/search changes.
     */
    public function updating($propertyName)
    {
        if (in_array($propertyName, ['search', 'readFilter', 'perPage', 'orderBy', 'sort'])) {
            $this->resetPage();
        }
    }

    /**
     * Show detail modal.
     */
    public function showDetail($questionId)
    {
        $this->selectedQuestionId = $questionId;
        $this->modalType = 'detail';
        $this->showModal = true;
    }

    /**
     * Confirm before marking as read.
     */
    public function confirmMarkAsRead($questionId)
    {
        $question = GuestQuestionModel::find($questionId);
        if (!$question) return;

        LivewireAlert::title('Konfirmasi')
            ->text('Tandai pertanyaan dari "' . $question->fullName . '" sebagai sudah dibaca?')
            ->question()
            ->withCancelButton('Batal')
            ->withConfirmButton('Ya, tandai sudah dibaca')
            ->onConfirm('markAsReadConfirmed', ['id' => $questionId])
            ->show();
    }

    /**
     * Execute after confirmation to mark as read.
     */
    public function markAsReadConfirmed($data)
    {
        $id = $data['id'] ?? null;
        $question = GuestQuestionModel::find($id);
        if ($question && !$question->is_read) {
            $question->is_read = true;
            $question->save();

            LivewireAlert::title('Berhasil')
                ->text('Pertanyaan berhasil ditandai sudah dibaca.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Confirm before deleting.
     */
    public function confirmDeleteQuestion($questionId)
    {
        $question = GuestQuestionModel::find($questionId);
        if (!$question) return;

        LivewireAlert::title('Konfirmasi')
            ->text('Hapus pertanyaan dari "' . $question->fullName . '" secara permanen?')
            ->question()
            ->withCancelButton('Batal')
            ->withConfirmButton('Ya, hapus')
            ->onConfirm('deleteQuestionConfirmed', ['id' => $questionId])
            ->show();
    }

    /**
     * Execute after confirmation to delete.
     */
    public function deleteQuestionConfirmed($data)
    {
        $id = $data['id'] ?? null;
        $question = GuestQuestionModel::find($id);
        if ($question) {
            $name = $question->fullName;
            $question->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Pertanyaan dari "' . $name . '" telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $guestQuestions = GuestQuestionModel::query()
            ->when($this->search, function ($query) {
                $query->where('fullName', 'like', "%{$this->search}%")
                      ->orWhere('formEmail', 'like', "%{$this->search}%")
                      ->orWhere('message', 'like', "%{$this->search}%");
            })
            ->when($this->readFilter === 'read', function ($query) {
                $query->where('is_read', true);
            })
            ->when($this->readFilter === 'unread', function ($query) {
                $query->where('is_read', false);
            })
            ->orderBy($this->orderBy, $this->sort)
            ->paginate($this->perPage);

        return view('livewire.managers.contents.guest-questions.index', compact('guestQuestions'));
    }

    /**
     * Sorting logic.
     */
    public function sortBy($column)
    {
        if ($this->orderBy === $column) {
            $this->sort = ($this->sort === 'asc') ? 'desc' : 'asc';
        } else {
            $this->orderBy = $column;
            $this->sort = 'asc';
        }
        $this->resetPage();
    }
}