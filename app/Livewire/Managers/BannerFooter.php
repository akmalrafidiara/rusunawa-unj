<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BannerFooter extends Component
{
    use WithFileUploads;

    // Banner Properties
    public $bannerTitle;
    public $bannerText;
    public $newDayaTarikValue = '';
    public $newDayaTarikLabel = '';
    public $dayaTariks = [];
    public $bannerImage;
    public $existingBannerImageUrl;

    // Footer Properties
    public $footerLogo;
    public $existingFooterLogoUrl;
    public $footerTitle;
    public $footerText;

    // Aturan validasi dasar
    protected $rules = [
        'bannerTitle' => 'required|string|max:255',
        'bannerText' => 'required|string|max:200',
        'dayaTariks' => 'array',
        'dayaTariks.*.value' => 'nullable|string|max:50', // DIUBAH DARI 'required' JADI 'nullable'
        'dayaTariks.*.label' => 'nullable|string|max:50', // DIUBAH DARI 'required' JADI 'nullable'
        'newDayaTarikValue' => 'nullable|string|max:50', // Ini akan divalidasi 'required' di addDayaTarik()
        'newDayaTarikLabel' => 'nullable|string|max:50', // Ini akan divalidasi 'required' di addDayaTarik()
        'bannerImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        'footerLogo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        'footerTitle' => 'required|string|max:255',
        'footerText' => 'required|string|max:200',
    ];

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'bannerTitle.required' => 'Kolom Judul Banner wajib diisi.',
        'bannerTitle.string' => 'Judul Banner harus berupa teks.',
        'bannerTitle.max' => 'Judul Banner tidak boleh lebih dari :max karakter.',

        'bannerText.required' => 'Kolom Teks Banner wajib diisi.',
        'bannerText.string' => 'Teks Banner harus berupa teks.',
        'bannerText.max' => 'Teks Banner tidak boleh lebih dari :max karakter.',

        'dayaTariks.array' => 'Daya Tarik harus berupa daftar.',
        // Pesan required untuk dayaTariks.*.value/label dihapus karena sekarang nullable
        'dayaTariks.*.value.string' => 'Nilai Daya Tarik harus berupa teks.',
        'dayaTariks.*.value.max' => 'Nilai Daya Tarik tidak boleh lebih dari :max karakter.',
        'dayaTariks.*.label.string' => 'Label Daya Tarik harus berupa teks.',
        'dayaTariks.*.label.max' => 'Label Daya Tarik tidak boleh lebih dari :max karakter.',

        'newDayaTarikValue.required' => 'Nilai Daya Tarik harus diisi.', // Tetap required untuk addDayaTarik()
        'newDayaTarikValue.string' => 'Nilai Daya Tarik harus berupa teks.',
        'newDayaTarikValue.max' => 'Nilai Daya Tarik tidak boleh lebih dari :max karakter.',
        'newDayaTarikLabel.required' => 'Label Daya Tarik harus diisi.', // Tetap required untuk addDayaTarik()
        'newDayaTarikLabel.string' => 'Label Daya Tarik harus berupa teks.',
        'newDayaTarikLabel.max' => 'Label Daya Tarik tidak boleh lebih dari :max karakter.',

        'bannerImage.required' => 'Foto Banner wajib diunggah.',
        'bannerImage.image' => 'File harus berupa gambar.',
        'bannerImage.max' => 'Ukuran gambar banner tidak boleh lebih dari 2MB.',
        'bannerImage.mimes' => 'Format gambar banner yang diizinkan adalah JPG, JPEG, atau PNG.',

        'footerLogo.required' => 'Logo Footer wajib diunggah.',
        'footerLogo.image' => 'File logo harus berupa gambar.',
        'footerLogo.max' => 'Ukuran logo footer tidak boleh lebih dari 2MB.',
        'footerLogo.mimes' => 'Format logo footer yang diizinkan adalah JPG, JPEG, atau PNG.',

        'footerTitle.required' => 'Kolom Judul Footer wajib diisi.',
        'footerTitle.string' => 'Judul Footer harus berupa teks.',
        'footerTitle.max' => 'Judul Footer tidak boleh lebih dari :max karakter.',

        'footerText.required' => 'Kolom Teks Footer wajib diisi.',
        'footerText.string' => 'Teks Footer harus berupa teks.',
        'footerText.max' => 'Teks Footer tidak boleh lebih dari :max karakter.',
    ];


    public function mount()
    {
        $this->bannerTitle = optional(Content::where('content_key', 'banner_title')->first())->content_value ?? '';
        $this->bannerText = optional(Content::where('content_key', 'banner_text')->first())->content_value ?? '';

        $dayaTariksContent = optional(Content::where('content_key', 'banner_daya_tariks')->first())->content_value;
        $loadedDayaTariks = is_array($dayaTariksContent) ? $dayaTariksContent : [];

        $this->dayaTariks = collect($loadedDayaTariks)->map(function ($item) {
            if (is_string($item)) {
                $parts = explode(' | ', $item, 2);
                return [
                    'value' => $parts[0] ?? '',
                    'label' => $parts[1] ?? ($parts[0] ?? '')
                ];
            }
            return [
                'value' => $item['value'] ?? '',
                'label' => $item['label'] ?? '',
            ];
        })->filter(function($item) {
            return !empty($item['value']) || !empty($item['label']); // Tetap filter item yang benar-benar kosong saat mount
        })->toArray();

        $this->existingBannerImageUrl = optional(Content::where('content_key', 'banner_image_url')->first())->content_value ?? '';
        $this->existingFooterLogoUrl = optional(Content::where('content_key', 'footer_logo_url')->first())->content_value ?? '';
        $this->footerTitle = optional(Content::where('content_key', 'footer_title')->first())->content_value ?? '';
        $this->footerText = optional(Content::where('content_key', 'footer_text')->first())->content_value ?? '';
    }

    public function addDayaTarik()
    {
        // Validasi ini akan tetap MEWAJIBKAN Value dan Label diisi saat tombol "Tambah Daya Tarik" diklik
        $this->validate([
            'newDayaTarikValue' => 'required|string|max:50',
            'newDayaTarikLabel' => 'required|string|max:50',
        ]);

        $newItem = [
            'value' => trim($this->newDayaTarikValue),
            'label' => trim($this->newDayaTarikLabel),
        ];

        foreach ($this->dayaTariks as $item) {
            if (
                strtolower($item['value']) === strtolower($newItem['value']) &&
                strtolower($item['label']) === strtolower($newItem['label'])
            ) {
                LivewireAlert::title('Daya tarik ini sudah ada dalam daftar!')
                    ->warning()
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
        }

        $this->dayaTariks[] = $newItem;

        $this->newDayaTarikValue = '';
        $this->newDayaTarikLabel = '';

        LivewireAlert::title('Daya tarik berhasil ditambahkan!')
            ->success()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function removeDayaTarik($indexToRemove)
    {
        if (isset($this->dayaTariks[$indexToRemove])) {
            unset($this->dayaTariks[$indexToRemove]);
            $this->dayaTariks = array_values($this->dayaTariks);
        }
        LivewireAlert::title('Daya tarik berhasil dihapus dari daftar!')
            ->info()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function saveBanner()
    {
        $rules = $this->rules;

        if (!$this->bannerImage && empty($this->existingBannerImageUrl)) {
            $rules['bannerImage'] = 'required|image|max:2048|mimes:jpg,jpeg,png';
        } else {
            $rules['bannerImage'] = 'nullable|image|max:2048|mimes:jpg,jpeg,png';
        }

        try {
            $this->validate($rules);
        } catch (ValidationException $e) {
            LivewireAlert::error()
                ->title('Mohon lengkapi semua data yang wajib diisi pada bagian Banner!')
                ->text('Harap periksa kembali kolom yang bertanda merah.')
                ->toast()
                ->position('top-end')
                ->show();
            throw $e;
        }

        Content::updateOrCreate(
            ['content_key' => 'banner_title'],
            ['content_value' => $this->bannerTitle, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'banner_text'],
            ['content_value' => $this->bannerText, 'content_type' => 'text']
        );

        // Daya Tarik akan disimpan meskipun ada item kosong, selama item tersebut 'nullable'
        Content::updateOrCreate(
            ['content_key' => 'banner_daya_tariks'],
            ['content_value' => $this->dayaTariks, 'content_type' => 'json']
        );

        if ($this->bannerImage) {
            $imagePath = $this->bannerImage->store('uploads/banner', 'public');
            $imageUrl = Storage::url($imagePath);

            Content::updateOrCreate(
                ['content_key' => 'banner_image_url'],
                ['content_value' => $imageUrl, 'content_type' => 'image_url']
            );
            $this->existingBannerImageUrl = $imageUrl;
            $this->bannerImage = null;
        }

        LivewireAlert::title('Konten Banner berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function saveFooter()
    {
        $rules = $this->rules;

        if (!$this->footerLogo && empty($this->existingFooterLogoUrl)) {
            $rules['footerLogo'] = 'required|image|max:2048|mimes:jpg,jpeg,png';
        } else {
            $rules['footerLogo'] = 'nullable|image|max:2048|mimes:jpg,jpeg,png';
        }

        try {
            $this->validate($rules);
        } catch (ValidationException $e) {
            LivewireAlert::error()
                ->title('Mohon lengkapi semua data yang wajib diisi pada bagian Footer!')
                ->text('Harap periksa kembali kolom yang bertanda merah.')
                ->toast()
                ->position('top-end')
                ->show();
            throw $e;
        }

        if ($this->footerLogo) {
            $logoPath = $this->footerLogo->store('uploads/footer', 'public');
            $logoUrl = Storage::url($logoPath);

            Content::updateOrCreate(
                ['content_key' => 'footer_logo_url'],
                ['content_value' => $logoUrl, 'content_type' => 'image_url']
            );
            $this->existingFooterLogoUrl = $logoUrl;
            $this->footerLogo = null;
        }

        Content::updateOrCreate(
            ['content_key' => 'footer_title'],
            ['content_value' => $this->footerTitle, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'footer_text'],
            ['content_value' => $this->footerText, 'content_type' => 'text']
        );

        LivewireAlert::title('Konten Footer berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function render()
    {
        return view('livewire.managers.contents.banner-footer.index');
    }
}