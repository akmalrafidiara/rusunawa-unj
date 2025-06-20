<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;

class About extends Component
{
    use WithFileUploads;

    public $aboutTitle;
    public $aboutDescription;
    public $aboutImage;
    public $existingAboutImageUrl;

    public $newDayaTarik = ''; // Mengubah nama properti dari newFacility menjadi newDayaTarik
    public $dayaTariks = [];   // Mengubah nama properti dari facilities menjadi dayaTariks

    // Aturan validasi dasar
    protected $rules = [
        'aboutTitle' => 'required|string|max:255',
        'aboutDescription' => 'required|string|max:200',
        'aboutImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png', // Default ke nullable
        'dayaTariks' => 'array', // Menggunakan dayaTariks
        'dayaTariks.*' => 'required|string|max:100', // Validasi untuk setiap item daya tarik
        'newDayaTarik' => 'nullable|string|max:100|min:3', // Validasi untuk input daya tarik baru
    ];

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'aboutTitle.required' => 'Kolom Judul Tentang Kami wajib diisi.',
        'aboutTitle.string' => 'Judul Tentang Kami harus berupa teks.',
        'aboutTitle.max' => 'Judul Tentang Kami tidak boleh lebih dari :max karakter.',

        'aboutDescription.required' => 'Kolom Teks Tentang Kami wajib diisi.',
        'aboutDescription.string' => 'Teks Tentang Kami harus berupa teks.',
        'aboutDescription.max' => 'Teks Tentang Kami tidak boleh lebih dari :max karakter.',

        'aboutImage.required' => 'Foto Tentang Kami wajib diunggah.', // Akan digunakan saat aturan diaktifkan dinamis
        'aboutImage.image' => 'File harus berupa gambar.',
        'aboutImage.max' => 'Ukuran gambar Foto Tentang Kami tidak boleh lebih dari 2MB.',
        'aboutImage.mimes' => 'Format gambar Foto Tentang Kami yang diizinkan adalah JPG, JPEG, atau PNG.',

        'dayaTariks.array' => 'Daftar daya tarik tidak valid.', // Menggunakan dayaTariks
        'dayaTariks.*.required' => 'Daya Tarik tidak boleh kosong.',
        'dayaTariks.*.string' => 'Daya Tarik harus berupa teks.',
        'dayaTariks.*.max' => 'Daya Tarik tidak boleh lebih dari :max karakter.',

        'newDayaTarik.required' => 'Daya Tarik baru wajib diisi.', // Menggunakan newDayaTarik
        'newDayaTarik.string' => 'Daya Tarik baru harus berupa teks.',
        'newDayaTarik.max' => 'Daya Tarik baru tidak boleh lebih dari :max karakter.',
        'newDayaTarik.min' => 'Daya Tarik baru minimal harus :min karakter.',
    ];


    public function mount()
    {
        $this->aboutTitle = optional(Content::where('content_key', 'about_us_title')->first())->content_value ?? '';
        $this->aboutDescription = optional(Content::where('content_key', 'about_us_description')->first())->content_value ?? '';
        $this->existingAboutImageUrl = optional(Content::where('content_key', 'about_us_image_url')->first())->content_value ?? '';

        // Mengubah content_key untuk daya tarik
        $dayaTariksContent = optional(Content::where('content_key', 'about_us_daya_tariks')->first())->content_value;
        $this->dayaTariks = is_array($dayaTariksContent) ? $dayaTariksContent : [];

        // Pastikan daya tarik diurutkan saat dimuat untuk konsistensi
        sort($this->dayaTariks);
    }

    public function addDayaTarik() // Mengubah nama metode dari addFacility menjadi addDayaTarik
    {
        $this->validateOnly('newDayaTarik');

        $normalizedNewDayaTarik = trim(strtolower($this->newDayaTarik));
        $normalizedExistingDayaTariks = array_map('strtolower', $this->dayaTariks);

        if (!empty($this->newDayaTarik) && !in_array($normalizedNewDayaTarik, $normalizedExistingDayaTariks)) {
            $this->dayaTariks[] = trim($this->newDayaTarik); // Simpan versi asli (tanpa lowercase)
            $this->newDayaTarik = '';
            sort($this->dayaTariks); // Urutkan setelah penambahan

            LivewireAlert::title('Daya tarik berhasil ditambahkan!')
            ->success()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
        } else if (!empty($this->newDayaTarik)) { // Jika input tidak kosong tapi duplikat
            LivewireAlert::title('Daya Tarik ini sudah ada dalam daftar!') // Pesan Bahasa Indonesia
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
        // Jika newDayaTarik kosong, validasiOnly akan menangani pesan error di bawah form.
    }

    public function removeDayaTarik($index) // Mengubah nama metode dari removeFacility menjadi removeDayaTarik
    {
        if (isset($this->dayaTariks[$index])) {
            unset($this->dayaTariks[$index]);
            $this->dayaTariks = array_values($this->dayaTariks); // Re-index array
            sort($this->dayaTariks); // Urutkan setelah penghapusan

            LivewireAlert::title('Daya tarik berhasil dihapus dari daftar!')
            ->info()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
        }
    }

    public function save()
    {
        // Salin aturan dasar
        $rules = $this->rules;

        // Logika kondisional untuk aboutImage:
        // Jika tidak ada file baru yang diunggah ($this->aboutImage kosong)
        // DAN tidak ada URL gambar yang sudah ada ($this->existingAboutImageUrl kosong)
        if (!$this->aboutImage && empty($this->existingAboutImageUrl)) {
            $rules['aboutImage'] = 'required|image|max:2048|mimes:jpg,jpeg,png';
        } else {
            // Jika ada file baru yang diunggah, atau tidak ada file baru tapi ada existing image,
            // maka gunakan aturan nullable.
            $rules['aboutImage'] = 'nullable|image|max:2048|mimes:jpg,jpeg,png';
        }

        // Lakukan validasi dengan aturan yang sudah disesuaikan
        $this->validate($rules);

        // 1. Simpan Judul Tentang Kami
        Content::updateOrCreate(
            ['content_key' => 'about_us_title'],
            ['content_value' => $this->aboutTitle, 'content_type' => 'text']
        );

        // 2. Simpan Teks Deskripsi
        Content::updateOrCreate(
            ['content_key' => 'about_us_description'],
            ['content_value' => $this->aboutDescription, 'content_type' => 'text']
        );

        // 3. Simpan Foto Tentang Kami (jika ada upload baru)
        if ($this->aboutImage) {
            // Livewire akan langsung memberikan objek UploadedFile ke $this->aboutImage
            $imagePath = $this->aboutImage->store('uploads/abouts', 'public');
            $imageUrl = Storage::url($imagePath);

            Content::updateOrCreate(
                ['content_key' => 'about_us_image_url'],
                ['content_value' => $imageUrl, 'content_type' => 'image_url']
            );
            $this->existingAboutImageUrl = $imageUrl;
            $this->aboutImage = null; // Reset properti upload setelah disimpan
        }
        // Tidak ada logika untuk menghapus gambar yang sudah ada tanpa menggantinya.
        // Jika Anda ingin fitur ini, perlu menambahkan tombol "Hapus Gambar" di view.


        // 4. Simpan Daya Tarik (sebagai JSON array)
        Content::updateOrCreate(
            ['content_key' => 'about_us_daya_tariks'], // Mengubah content_key di database
            ['content_value' => $this->dayaTariks, 'content_type' => 'json']
        );

        // Notifikasi sukses setelah semua penyimpanan
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