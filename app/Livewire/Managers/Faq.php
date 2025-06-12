<?php

namespace App\Livewire\Managers;

use App\Models\Faq as FaqModel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Faq extends Component
{
    public $search = '';
    public $question, $answer;

    public $answerFilter = '';

    public $orderBy = 'created_at';
    public $sort = 'asc';

    public $showModal = false;
    public $faqIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'answerFilter' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function render()
    {
        $faqs = FaqModel::query()
            ->when($this->search, fn($q) => $q->where('question', 'like', "%{$this->search}%"))
            ->when($this->answerFilter, fn($q) => $q->where('answer', 'like', "%{$this->answerFilter}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        return view('livewire.managers.faq', compact('faqs'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(FaqModel $faq)
    {
        $this->faqIdBeingEdited = $faq->id;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->showModal = true;
    }

    public function rules()
    {
        return [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'question' => $this->question,
            'answer' => $this->answer,
        ];

        FaqModel::updateOrCreate(
            ['id' => $this->faqIdBeingEdited],
            $data
        );

        LivewireAlert::title($this->faqIdBeingEdited ? 'FAQ berhasil diperbarui.' : 'FAQ berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus FAQ?')
            ->text('Apakah Anda yakin ingin menghapus FAQ ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteFaq', ['id' => $data['id']])
            ->show();
    }

    public function deleteFaq($data)
    {
        $id = $data['id'];
        $faq = FaqModel::find($id);
        if ($faq) {
            $faq->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('FAQ telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function resetForm()
    {
        $this->question = '';
        $this->answer = '';
        $this->faqIdBeingEdited = null;
    }
}
