<?php

namespace App\Livewire\Managers;

use App\Models\Faq as FaqModel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Faq extends Component
{
    use WithPagination;
    
    public $search = '';
    public $question;
    public $answer; // Properti ini akan diisi oleh Trix editor

    public $answerFilter = '';

    public $orderBy = 'priority';
    public $sort = 'asc';

    public $showModal = false;
    public $faqIdBeingEdited = null;
    public $maxPriority = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'answerFilter' => ['except' => ''],
        'orderBy' => ['except' => 'priority'],
        'sort' => ['except' => 'asc'],
    ];

    // Tambahkan listener untuk event dari Trix editor
    protected $listeners = ['contentChanged' => 'updateAnswer'];

    public function updateAnswer($content)
    {
        $this->answer = $content;
    }

    public function render()
    {
        $faqs = FaqModel::query()
            ->when($this->search, fn($q) => $q->where('question', 'like', "%{$this->search}%"))
            ->when($this->answerFilter, fn($q) => $q->where('answer', 'like', "%{$this->answerFilter}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        // Ambil prioritas maksimum, jika tidak ada, default ke 0 (karena +1 nanti jadi 1)
        $this->maxPriority = FaqModel::max('priority') ?? 0;

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

        // Penting: Emit event setelah data dimuat untuk menginisialisasi Trix
        $this->dispatch('trix-load-content', $this->answer);
    }

    public function rules()
    {
        return [
            'question' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faq')->ignore($this->faqIdBeingEdited),
            ],
            'answer' => 'required|string', // Validasi tetap sama, meskipun inputnya WYSIWYG
        ];
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'question' => $this->question,
            'answer' => $this->answer,
        ];

        if (!$this->faqIdBeingEdited) {
            $maxPriority = FaqModel::max('priority');
            // Jika tidak ada FAQ, prioritas dimulai dari 1. Jika ada, prioritas maksimum + 1.
            $data['priority'] = ($maxPriority !== null) ? $maxPriority + 1 : 1;
        }

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
            // Dekremen prioritas hanya untuk FAQ yang prioritasnya lebih besar dari FAQ yang dihapus
            FaqModel::where('priority', '>', $faq->priority)->decrement('priority');
            $faq->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('FAQ telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function moveUp(FaqModel $faq)
    {
        // Temukan FAQ sebelumnya dengan prioritas lebih kecil dari FAQ saat ini
        $previousFaq = FaqModel::where('priority', '<', $faq->priority)
                                ->orderBy('priority', 'desc')
                                ->first();

        if ($previousFaq) {
            $tempPriority = $faq->priority;
            $faq->update(['priority' => $previousFaq->priority]);
            $previousFaq->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function moveDown(FaqModel $faq)
    {
        // Temukan FAQ berikutnya dengan prioritas lebih besar dari FAQ saat ini
        $nextFaq = FaqModel::where('priority', '>', $faq->priority)
                            ->orderBy('priority', 'asc')
                            ->first();

        if ($nextFaq) {
            $tempPriority = $faq->priority;
            $faq->update(['priority' => $nextFaq->priority]);
            $nextFaq->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function resetForm()
    {
        $this->reset(['question', 'answer', 'faqIdBeingEdited']);
        // Emit event untuk mereset Trix editor di sisi client
        $this->dispatch('trix-reset');
    }
}