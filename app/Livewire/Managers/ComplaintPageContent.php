<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ComplaintPageContent extends Component 
{
    use WithFileUploads;

    public $complaintTitle; 
    public $complaintDescription; 
    public $complaintImage; 
    public $existingComplaintImageUrl; 

    public $newAdvantage = ''; 
    public $advantages = []; 

    // Aturan validasi
    protected $rules = [
        'complaintTitle' => 'required|string|max:255',
        'complaintDescription' => 'required|string|max:500',
        'complaintImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        'advantages' => 'array',
        'advantages.*' => 'nullable|string|max:100',
        'newAdvantage' => 'nullable|string|max:100|min:3',
    ];

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'complaintTitle.required' => 'Kolom Judul Pengaduan wajib diisi.',
        'complaintTitle.string' => 'Judul Pengaduan harus berupa teks.',
        'complaintTitle.max' => 'Judul Pengaduan tidak boleh lebih dari :max karakter.',

        'complaintDescription.required' => 'Kolom Deskripsi Pengaduan wajib diisi.',
        'complaintDescription.string' => 'Deskripsi Pengaduan harus berupa teks.',
        'complaintDescription.max' => 'Deskripsi Pengaduan tidak boleh lebih dari :max karakter.',

        'complaintImage.required' => 'Foto Pengaduan wajib diunggah.',
        'complaintImage.image' => 'File harus berupa gambar.',
        'complaintImage.max' => 'Ukuran gambar Foto Pengaduan tidak boleh lebih dari 2MB.',
        'complaintImage.mimes' => 'Format gambar Foto Pengaduan yang diizinkan adalah JPG, JPEG, atau PNG.',

        'advantages.array' => 'Daftar keunggulan tidak valid.',
        'advantages.*.required' => 'Keunggulan tidak boleh kosong.',
        'advantages.*.string' => 'Keunggulan harus berupa teks.',
        'advantages.*.max' => 'Keunggulan tidak boleh lebih dari :max karakter.',

        'newAdvantage.required' => 'Keunggulan baru wajib diisi.',
        'newAdvantage.string' => 'Keunggulan baru harus berupa teks.',
        'newAdvantage.max' => 'Keunggulan baru tidak boleh lebih dari :max karakter.',
        'newAdvantage.min' => 'Keunggulan baru minimal harus :min karakter.',
    ];


    public function mount()
    {
        $this->complaintTitle = optional(Content::where('content_key', 'complaint_service_title')->first())->content_value ?? '';
        $this->complaintDescription = optional(Content::where('content_key', 'complaint_service_description')->first())->content_value ?? '';
        $this->existingComplaintImageUrl = optional(Content::where('content_key', 'complaint_service_image_url')->first())->content_value ?? '';

        $advantagesContent = optional(Content::where('content_key', 'complaint_service_advantages')->first())->content_value;
        $this->advantages = is_array($advantagesContent) ? $advantagesContent : [];
    }

    public function addAdvantage()
    {
        $this->validateOnly('newAdvantage');

        $normalizedNewAdvantage = trim(strtolower($this->newAdvantage));
        $normalizedExistingAdvantages = array_map('strtolower', $this->advantages);

        if (!empty($this->newAdvantage) && !in_array($normalizedNewAdvantage, $normalizedExistingAdvantages)) {
            $this->advantages[] = trim($this->newAdvantage);
            $this->newAdvantage = '';

            LivewireAlert::title('Keunggulan berhasil ditambahkan!')
            ->success()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
        } else if (!empty($this->newAdvantage)) {
            LivewireAlert::title('Keunggulan ini sudah ada dalam daftar!')
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function removeAdvantage($index)
    {
        if (isset($this->advantages[$index])) {
            unset($this->advantages[$index]);
            $this->advantages = array_values($this->advantages);
            sort($this->advantages);

            LivewireAlert::title('Keunggulan berhasil dihapus dari daftar!')
            ->info()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
        }
    }

    public function save()
    {
        $rules = $this->rules;

        if (!$this->complaintImage && empty($this->existingComplaintImageUrl)) {
            $rules['complaintImage'] = 'required|image|max:2048|mimes:jpg,jpeg,png';
        } else {
            $rules['complaintImage'] = 'nullable|image|max:2048|mimes:jpg,jpeg,png';
        }

        try {
            $this->validate($rules);
        } catch (ValidationException $e) {
            LivewireAlert::error()
                ->title('Mohon lengkapi semua data yang wajib diisi pada bagian Layanan Pengaduan!')
                ->text('Harap periksa kembali kolom yang bertanda merah.')
                ->toast()
                ->position('top-end')
                ->show();
            throw $e;
        }

        // 1. Simpan Judul Pengaduan
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_title'],
            ['content_value' => $this->complaintTitle, 'content_type' => 'text']
        );

        // 2. Simpan Deskripsi Pengaduan
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_description'],
            ['content_value' => $this->complaintDescription, 'content_type' => 'text']
        );

        // 3. Simpan Foto Pengaduan (jika ada upload baru)
        if ($this->complaintImage) {
            $imagePath = $this->complaintImage->store('uploads/complaints', 'public'); 
            $imageUrl = Storage::url($imagePath);

            Content::updateOrCreate(
                ['content_key' => 'complaint_service_image_url'],
                ['content_value' => $imageUrl, 'content_type' => 'image_url']
            );
            $this->existingComplaintImageUrl = $imageUrl;
            $this->complaintImage = null;
        }

        // 4. Simpan Keunggulan (sebagai JSON array)
        Content::updateOrCreate(
            ['content_key' => 'complaint_service_advantages'], // Mengubah content_key di database
            ['content_value' => $this->advantages, 'content_type' => 'json']
        );

        // Notifikasi sukses setelah semua penyimpanan
        LivewireAlert::title('Konten Layanan Pengaduan berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function render()
    {
        return view('livewire.managers.contents.complaint-page-content.index'); 
    }
}