<?php

namespace App\Livewire\Managers;

use App\Models\Galleries as GalleryModel;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Spatie\LivewireFilepond\WithFilePond;

class Galleries extends Component
{
    use WithFileUploads;
    use WithFilePond;

    public $caption, $image, $temporaryImage;
    public $search = '';
    public $orderBy = 'created_at';
    public $sort = 'asc';
    public $showModal = false;
    public $galleryIdBeingEdited = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'orderBy' => ['except' => 'created_at'],
        'sort' => ['except' => 'asc'],
    ];

    public function render()
    {
        $galleries = GalleryModel::query()
            ->when($this->search, fn($q) => $q->where('caption', 'like', "%{$this->search}%"))
            ->orderBy($this->orderBy, $this->sort)
            ->paginate(10);

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

    public function rules()
    {
        return [
            'caption' => 'required|string|max:255',
            'image' => $this->galleryIdBeingEdited && $this->image === $this->temporaryImage
                ? 'nullable'
                : 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
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

        // Only delete old image if a new image is being uploaded or the image is being cleared
        if ($this->image !== $this->temporaryImage && $this->temporaryImage != null) {
            Storage::disk('public')->delete($this->temporaryImage);
            // If the new image is empty, ensure the database field is null
            if (empty($this->image)) {
                 $data['image'] = null;
            }
        }

        if ($this->image instanceof TemporaryUploadedFile) {
            $data['image'] = $this->image->store('galleries', 'public');
        } elseif (empty($this->image) && $this->galleryIdBeingEdited && $this->temporaryImage != null) {
            // This handles the case where an existing image is cleared without a new one
            $data['image'] = null;
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

    // --- PERBAIKAN DIMULAI DI SINI ---
    public function confirmDelete($id) // Ganti $data menjadi $id
    {
        LivewireAlert::title('Hapus galeri?')
            ->text('Apakah Anda yakin ingin menghapus galeri ini?')
            ->question()
            ->withCancelButton('Batalkan')
            ->withConfirmButton('Hapus!')
            // Sekarang Anda melewatkan $id langsung ke array untuk deleteGallery
            ->onConfirm('deleteGallery', ['id' => $id])
            ->show();
    }

    public function deleteGallery($data)
    {
        // $data sekarang DIJAMIN adalah array dengan kunci 'id' karena cara kita mengirimnya dari onConfirm
        $id = $data['id'];
        $gallery = GalleryModel::find($id);
        if ($gallery) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            $gallery->delete();

            LivewireAlert::title('Berhasil Dihapus')
                ->text('Galeri telah dihapus.')
                ->success()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }
    // --- PERBAIKAN BERAKHIR DI SINI ---

    public function resetForm()
    {
        $this->caption = '';
        $this->image = '';
        $this->temporaryImage = '';
        $this->galleryIdBeingEdited = null;
    }
}