<?php

namespace App\Livewire\Managers;

use App\Models\Galleries as GalleryModel; // Tetap Galleries sesuai preferensi Anda
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Spatie\LivewireFilepond\WithFilePond; // Pastikan ini diinstal dan dikonfigurasi jika digunakan

class Galleries extends Component
{
    use WithFileUploads;
    use WithFilePond;
    use withPagination;

    public $caption, $image, $temporaryImage;
    public $search = '';
    public $orderBy = 'priority'; // Ubah default order ke 'priority'
    public $sort = 'asc'; // Default sort ascending
    public $showModal = false;
    public $galleryIdBeingEdited = null;
    public $maxPriority = 0; // Tambahkan properti untuk prioritas maksimum

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'priority'],
        'sort' => ['except' => 'asc'],
    ];

    public function rules()
    {
        return [
            'caption' => 'required|string|max:255',
            'image' => $this->galleryIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'caption.required' => 'Kolom Deskripsi Gambar wajib diisi.',
        'caption.string' => 'Deskripsi Gambar harus berupa teks.',
        'caption.max' => 'Deskripsi Gambar tidak boleh lebih dari :max karakter.',
    ];

    public function mount()
    {
        $this->image = '';
        $this->temporaryImage = '';
        $this->caption = '';
        $this->galleryIdBeingEdited = null;
        $this->maxPriority = GalleryModel::max('priority') ?? 0;
    }

    public function render()
    {
        $galleries = GalleryModel::query()
            ->when($this->search, fn($q) => $q->where('caption', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

        // Hitung prioritas maksimum saat render
        $this->maxPriority = GalleryModel::max('priority') ?? 0;

        return view('livewire.managers.contents.galleries.index', compact('galleries'));
    }

    public function create()
    {
        $this->search = '';
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(GalleryModel $gallery)
    {
        $this->galleryIdBeingEdited = $gallery->id;
        $this->caption = $gallery->caption;
        $this->image = $gallery->image;
        $this->temporaryImage = $gallery->image;
        $this->showModal = true;
    }

    public function validateUploadedFile()
    {
        $this->validate([
            'image' => $this->rules()['image'],
        ]);
        return true;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function save()
    {
        $this->validate($this->rules());

        $data = [
            'caption' => $this->caption,
        ];

        // Logic untuk menghandle gambar
        if ($this->image instanceof TemporaryUploadedFile) {
            if ($this->galleryIdBeingEdited && $this->temporaryImage) {
                Storage::disk('public')->delete($this->temporaryImage);
            }
            $data['image'] = $this->image->store('galleries', 'public');
        } elseif ($this->galleryIdBeingEdited && empty($this->image) && $this->temporaryImage) {
            Storage::disk('public')->delete($this->temporaryImage);
            $data['image'] = null;
        } elseif ($this->galleryIdBeingEdited && !empty($this->temporaryImage) && $this->image === $this->temporaryImage) {
             $data['image'] = $this->temporaryImage;
        } else {
            $data['image'] = null;
        }

        // --- Logika Priority Ditambahkan di Sini ---
        if (!$this->galleryIdBeingEdited) {
            $maxPriority = GalleryModel::max('priority');
            $data['priority'] = ($maxPriority !== null) ? $maxPriority + 1 : 1;
        }

        GalleryModel::updateOrCreate(
            ['id' => $this->galleryIdBeingEdited],
            $data
        );

        LivewireAlert::title($this->galleryIdBeingEdited ? 'Galeri berhasil diperbarui.' : 'Galeri berhasil ditambahkan.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        $this->resetForm();
        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        LivewireAlert::title('Hapus galeri?')
            ->text('Apakah Anda yakin ingin menghapus galeri ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            ->onConfirm('deleteGallery', ['id' => $id])
            ->show();
    }

    public function deleteGallery($data)
    {
        $id = $data['id'];
        $gallery = GalleryModel::find($id);
        if ($gallery) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            GalleryModel::where('priority', '>', $gallery->priority)->decrement('priority');

            $gallery->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Galeri telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    //  Prioritas (moveUp, moveDown)
    public function moveUp(GalleryModel $gallery)
    {
        $previousGallery = GalleryModel::where('priority', '<', $gallery->priority)
                                        ->orderBy('priority', 'desc')
                                        ->first();

        if ($previousGallery) {
            $tempPriority = $gallery->priority;
            $gallery->update(['priority' => $previousGallery->priority]);
            $previousGallery->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function moveDown(GalleryModel $gallery)
    {
        $nextGallery = GalleryModel::where('priority', '>', $gallery->priority)
                                    ->orderBy('priority', 'asc')
                                    ->first();

        if ($nextGallery) {
            $tempPriority = $gallery->priority;
            $gallery->update(['priority' => $nextGallery->priority]);
            $nextGallery->update(['priority' => $tempPriority]);

            LivewireAlert::success('Prioritas berhasil diubah.')->toast()->position('top-end');
        }
    }

    public function resetForm()
    {
        $this->caption = '';
        $this->image = '';
        $this->temporaryImage = '';
        $this->galleryIdBeingEdited = null;
    }
}