<?php

namespace App\Livewire\Managers;

use App\Models\Regulation as RegulationModel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Regulation extends Component
{
    use WithPagination;

    public $search = '';
    public $title;
    public $content; // Properti ini akan diisi oleh Trix editor

    public $contentFilter = '';

    public $orderBy = 'priority'; // Menggunakan priority sebagai default order
    public $sort = 'asc'; // Default sort ascending

    public $showModal = false;
    public $regulationIdBeingEdited = null;
    public $maxPriority = 0; // Mengembalikan properti maxPriority

    protected $queryString = [
        'search' => ['except' => ''],
        'contentFilter' => ['except' => ''],
        'orderBy' => ['except' => 'priority'], // Kembali diaktifkan
        'sort' => ['except' => 'asc'], // Kembali diaktifkan
    ];

    // Tambahkan listener untuk event dari Trix editor
    protected $listeners = ['contentChanged' => 'updateContent'];

    public function updateContent($content)
    {
        $this->content = $content;
    }

    public function render()
    {
        $regulations = RegulationModel::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->contentFilter, fn($q) => $q->where('content', 'like', "%{$this->contentFilter}%"))
            ->orderBy($this->orderBy, $this->sort) // Order berdasarkan priority
            ->paginate(10);

        // Ambil prioritas maksimum, jika tidak ada, default ke 0
        $this->maxPriority = RegulationModel::max('priority') ?? 0;

        return view('livewire.managers.contents.regulation.index', compact('regulations'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(RegulationModel $regulation)
    {
        $this->regulationIdBeingEdited = $regulation->id;
        $this->title = $regulation->title;
        $this->content = $regulation->content;
        $this->showModal = true;

        // Penting: Emit event setelah data dimuat untuk menginisialisasi Trix
        $this->dispatch('trix-load-content', $this->content);
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regulations')->ignore($this->regulationIdBeingEdited),
            ],
            'content' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'title' => $this->title,
            'content' => $this->content,
        ];

        // Logika prioritas kembali di sini
        if (!$this->regulationIdBeingEdited) {
            $maxPriority = RegulationModel::max('priority');
            // Jika tidak ada regulasi, prioritas dimulai dari 1. Jika ada, prioritas maksimum + 1.
            $data['priority'] = ($maxPriority !== null) ? $maxPriority + 1 : 1;
        }

        RegulationModel::updateOrCreate(
            ['id' => $this->regulationIdBeingEdited],
            $data
        );

        LivewireAlert::title($this->regulationIdBeingEdited ? 'Regulasi berhasil diperbarui.' : 'Regulasi berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    public function confirmDelete($data)
    {
        LivewireAlert::title('Hapus Regulasi?')
            ->text('Apakah Anda yakin ingin menghapus regulasi ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteRegulation', ['id' => $data['id']])
            ->show();
    }

    public function deleteRegulation($data)
    {
        $id = $data['id'];
        $regulation = RegulationModel::find($id);
        if ($regulation) {
            // Dekremen prioritas hanya untuk regulasi yang prioritasnya lebih besar dari regulasi yang dihapus
            RegulationModel::where('priority', '>', $regulation->priority)->decrement('priority');
            $regulation->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Regulasi telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    // Metode moveUp dan moveDown kembali diaktifkan
    public function moveUp(RegulationModel $regulation)
    {
        // Temukan regulasi sebelumnya dengan prioritas lebih kecil dari regulasi saat ini
        $previousRegulation = RegulationModel::where('priority', '<', $regulation->priority)
                                        ->orderBy('priority', 'desc')
                                        ->first();

        if ($previousRegulation) {
            $tempPriority = $regulation->priority;
            $regulation->update(['priority' => $previousRegulation->priority]);
            $previousRegulation->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function moveDown(RegulationModel $regulation)
    {
        // Temukan regulasi berikutnya dengan prioritas lebih besar dari regulasi saat ini
        $nextRegulation = RegulationModel::where('priority', '>', $regulation->priority)
                                    ->orderBy('priority', 'asc')
                                    ->first();

        if ($nextRegulation) {
            $tempPriority = $regulation->priority;
            $regulation->update(['priority' => $nextRegulation->priority]);
            $nextRegulation->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function resetForm()
    {
        $this->reset(['title', 'content', 'regulationIdBeingEdited']);
        // Emit event untuk mereset Trix editor di sisi client
        $this->dispatch('trix-reset');
    }
}