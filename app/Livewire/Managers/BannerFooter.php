<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str; // UUID tidak lagi digunakan

class BannerFooter extends Component
{
    use WithFileUploads;

    // Banner Properties
    public $bannerTitle;
    public $bannerText;
    public $newDayaTarikLabel = ''; 
    public $newDayaTarikValue = ''; 
    public $dayaTariks = [];       // Array of associative arrays: [{'value': '...', 'label': '...'}]
    public $bannerImage; 
    public $existingBannerImageUrl; 

    // Footer Properties
    public $footerLogo; 
    public $existingFooterLogoUrl; 
    public $footerTitle;
    public $footerText;

    protected $rules = [
        // Banner Rules
        'bannerTitle' => 'required|string|max:255',
        'bannerText' => 'required|string|max:200',
        'dayaTariks' => 'array',
        'dayaTariks.*.value' => 'required|string|max:50', // Validasi value setiap item
        'dayaTariks.*.label' => 'required|string|max:50', // Validasi label setiap item
        // '_id' dan 'display' tidak lagi divalidasi di sini
        'newDayaTarikValue' => 'required|string|max:50', 
        'newDayaTarikLabel' => 'required|string|max:50', 
        'bannerImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',

        // Footer Rules
        'footerLogo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        'footerTitle' => 'required|string|max:255',
        'footerText' => 'required|string|max:200',
    ];

    public function mount()
    {
        $this->bannerTitle = optional(Content::where('content_key', 'banner_title')->first())->content_value ?? '';
        $this->bannerText = optional(Content::where('content_key', 'banner_text')->first())->content_value ?? '';
        
        $dayaTariksContent = optional(Content::where('content_key', 'banner_daya_tariks')->first())->content_value;
        $loadedDayaTariks = is_array($dayaTariksContent) ? $dayaTariksContent : [];

        // Saat dimuat, hanya ambil value dan label (abaikan _id dan display jika ada dari data lama)
        $this->dayaTariks = collect($loadedDayaTariks)->map(function ($item) {
            return [
                'value' => $item['value'] ?? ($item['display'] ?? ''), // Pastikan value ada, fallback ke display
                'label' => $item['label'] ?? ($item['display'] ?? ''), // Pastikan label ada, fallback ke display
            ];
        })->toArray();

        $this->existingBannerImageUrl = optional(Content::where('content_key', 'banner_image_url')->first())->content_value ?? '';

        $this->existingFooterLogoUrl = optional(Content::where('content_key', 'footer_logo_url')->first())->content_value ?? '';
        $this->footerTitle = optional(Content::where('content_key', 'footer_title')->first())->content_value ?? '';
        $this->footerText = optional(Content::where('content_key', 'footer_text')->first())->content_value ?? '';
    }

    public function addDayaTarik()
    {
        $this->validate([
            'newDayaTarikValue' => 'required|string|max:50',
            'newDayaTarikLabel' => 'required|string|max:50',
        ], [
            'newDayaTarikValue.required' => 'Value daya tarik harus diisi.',
            'newDayaTarikLabel.required' => 'Label daya tarik harus diisi.',
            'newDayaTarikValue.max' => 'Value daya tarik tidak boleh lebih dari :max karakter.',
            'newDayaTarikLabel.max' => 'Label daya tarik tidak boleh lebih dari :max karakter.',
        ]);

        // Buat item baru hanya dengan value dan label
        $newItem = [
            'value' => $this->newDayaTarikValue,
            'label' => $this->newDayaTarikLabel,
        ];

        // Cek duplikasi (berdasarkan kombinasi value dan label)
        foreach ($this->dayaTariks as $item) {
            if ($item['value'] === $newItem['value'] && $item['label'] === $newItem['label']) {
                LivewireAlert::title('Daya tarik ini sudah ada!')
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

        // Urutkan array berdasarkan label (opsional)
        usort($this->dayaTariks, fn($a, $b) => $a['label'] <=> $b['label']);

        LivewireAlert::title('Daya tarik berhasil ditambahkan!')
            ->success()
            ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function removeDayaTarik($indexToRemove) // Menerima indeks, bukan ID
    {
        if (isset($this->dayaTariks[$indexToRemove])) {
            unset($this->dayaTariks[$indexToRemove]);
            $this->dayaTariks = array_values($this->dayaTariks); // Re-index array
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
        $this->validate([
            'bannerTitle' => 'required|string|max:255',
            'bannerText' => 'required|string|max:200',
            'dayaTariks' => 'array',
            'dayaTariks.*.value' => 'required|string|max:50', 
            'dayaTariks.*.label' => 'required|string|max:50', 
            // '_id' dan 'display' tidak lagi divalidasi
            'bannerImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        Content::updateOrCreate(
            ['content_key' => 'banner_title'],
            ['content_value' => $this->bannerTitle, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'banner_text'],
            ['content_value' => $this->bannerText, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'banner_daya_tariks'],
            ['content_value' => $this->dayaTariks, 'content_type' => 'json'] // Akan disimpan sebagai array of {value, label}
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
        $this->validate([
            'footerLogo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'footerTitle' => 'required|string|max:255',
            'footerText' => 'required|string|max:200',
        ]);

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