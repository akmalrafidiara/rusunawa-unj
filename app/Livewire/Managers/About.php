<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads; // Tetap gunakan trait ini untuk upload
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;

class About extends Component
{
    use WithFileUploads;

    public $aboutTitle;
    public $aboutDescription; // Untuk "Teks Banner"
    public $aboutImage;       // Properti untuk menyimpan file yang diupload (objek UploadedFile)
    public $existingAboutImageUrl; // URL gambar yang sudah ada di storage

    public $newFacility = '';      // Input untuk menambahkan fasilitas baru
    public $facilities = [];       // Array untuk menyimpan daftar fasilitas

    // Tidak ada listener FilePond lagi

    protected $rules = [
        'aboutTitle' => 'required|string|max:255',
        'aboutDescription' => 'required|string|max:500',
        'aboutImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png', // Validasi langsung pada objek file upload
        'facilities' => 'array',
        'facilities.*' => 'required|string|max:100',
        'newFacility' => 'nullable|string|max:100|min:3',
    ];

    public function mount()
    {
        $this->aboutTitle = optional(Content::where('content_key', 'about_us_title')->first())->content_value ?? '';
        $this->aboutDescription = optional(Content::where('content_key', 'about_us_description')->first())->content_value ?? '';
        $this->existingAboutImageUrl = optional(Content::where('content_key', 'about_us_image_url')->first())->content_value ?? '';

        $facilitiesContent = optional(Content::where('content_key', 'about_us_facilities')->first())->content_value;
        $this->facilities = is_array($facilitiesContent) ? $facilitiesContent : [];

        // Tidak ada dispatch untuk FilePond lagi
    }

    public function addFacility()
    {
        $this->validateOnly('newFacility');

        if (!empty($this->newFacility) && !in_array($this->newFacility, $this->facilities)) {
            $this->facilities[] = $this->newFacility;
            $this->newFacility = '';
            sort($this->facilities);
        } else if (!empty($this->newFacility)) {
            LivewireAlert::title('Fasilitas sudah ada!')
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function removeFacility($index)
    {
        if (isset($this->facilities[$index])) {
            unset($this->facilities[$index]);
            $this->facilities = array_values($this->facilities);
        }
    }

    public function save()
    {
        $this->validate();

        // 1. Simpan Judul Tentang Kami
        Content::updateOrCreate(
            ['content_key' => 'about_us_title'],
            ['content_value' => $this->aboutTitle, 'content_type' => 'text']
        );

        // 2. Simpan Teks Banner
        Content::updateOrCreate(
            ['content_key' => 'about_us_description'],
            ['content_value' => $this->aboutDescription, 'content_type' => 'text']
        );

        // 3. Simpan Foto Tentang Kami (jika ada upload baru)
        if ($this->aboutImage) {
            // Livewire akan langsung memberikan objek UploadedFile ke $this->aboutImage
            // Pindahkan file dari temporary location ke public storage
            $imagePath = $this->aboutImage->store('uploads/abouts', 'public'); // 'uploads/abouts' is folder, 'public' is disk
            $imageUrl = Storage::url($imagePath);

            Content::updateOrCreate(
                ['content_key' => 'about_us_image_url'],
                ['content_value' => $imageUrl, 'content_type' => 'image_url']
            );
            $this->existingAboutImageUrl = $imageUrl; // Update URL yang ditampilkan
            $this->aboutImage = null; // Reset properti upload setelah disimpan
        }
        // Jika Anda ingin opsi untuk menghapus gambar yang sudah ada tanpa menggantinya,
        // Anda perlu menambahkan tombol "Hapus Gambar" terpisah di view.

        // 4. Simpan Fasilitas (sebagai JSON array)
        Content::updateOrCreate(
            ['content_key' => 'about_us_facilities'],
            ['content_value' => $this->facilities, 'content_type' => 'json']
        );

        LivewireAlert::title('Konten Tentang Kami berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function render()
    {
        return view('livewire.managers.contents.abouts.index');
    }
}