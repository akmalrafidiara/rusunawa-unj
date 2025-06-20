<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Storage;

class BannerFooter extends Component
{
    use WithFileUploads;

    // Banner Properties
    public $bannerTitle;
    public $bannerText; // Sesuai dengan "Teks Banner" di UI
    public $newDayaTarik = ''; // Kembali ke newDayaTarik
    public $dayaTariks = [];  // Kembali ke dayaTariks
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
        'dayaTariks' => 'array', // Kembali ke dayaTariks
        'dayaTariks.*' => 'required|string|max:100', // Kembali ke dayaTariks.*
        'newDayaTarik' => 'nullable|string|max:100|min:3', // Kembali ke newDayaTarik
        'bannerImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',

        // Footer Rules
        'footerLogo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        'footerTitle' => 'required|string|max:255',
        'footerText' => 'required|string|max:200',
    ];

    public function mount()
    {
        // Load Banner Content
        $this->bannerTitle = optional(Content::where('content_key', 'banner_title')->first())->content_value ?? '';
        $this->bannerText = optional(Content::where('content_key', 'banner_text')->first())->content_value ?? '';
        // content_key kembali ke 'banner_daya_tariks'
        $dayaTariksContent = optional(Content::where('content_key', 'banner_daya_tariks')->first())->content_value;
        $this->dayaTariks = is_array($dayaTariksContent) ? $dayaTariksContent : [];
        $this->existingBannerImageUrl = optional(Content::where('content_key', 'banner_image_url')->first())->content_value ?? '';

        // Load Footer Content
        $this->existingFooterLogoUrl = optional(Content::where('content_key', 'footer_logo_url')->first())->content_value ?? '';
        $this->footerTitle = optional(Content::where('content_key', 'footer_title')->first())->content_value ?? '';
        $this->footerText = optional(Content::where('content_key', 'footer_text')->first())->content_value ?? '';
    }

    // Daya Tarik Methods (for Banner) - Kembali ke nama method asli
    public function addDayaTarik()
    {
        $this->validateOnly('newDayaTarik');

        if (!empty($this->newDayaTarik) && !in_array($this->newDayaTarik, $this->dayaTariks)) {
            $this->dayaTariks[] = $this->newDayaTarik;
            $this->newDayaTarik = '';
            sort($this->dayaTariks);
        } else if (!empty($this->newDayaTarik)) {
            LivewireAlert::title('Daya tarik sudah ada!') // Pesan alert disesuaikan
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function removeDayaTarik($index) // Kembali ke nama method asli
    {
        if (isset($this->dayaTariks[$index])) {
            unset($this->dayaTariks[$index]);
            $this->dayaTariks = array_values($this->dayaTariks);
        }
    }

    public function saveBanner()
    {
        // Validate only banner related fields
        $this->validate([
            'bannerTitle' => 'required|string|max:255',
            'bannerText' => 'required|string|max:200',
            'dayaTariks' => 'array',
            'dayaTariks.*' => 'required|string|max:100',
            'bannerImage' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Save Banner Title
        Content::updateOrCreate(
            ['content_key' => 'banner_title'],
            ['content_value' => $this->bannerTitle, 'content_type' => 'text']
        );

        // Save Banner Text
        Content::updateOrCreate(
            ['content_key' => 'banner_text'],
            ['content_value' => $this->bannerText, 'content_type' => 'text']
        );

        // Save Banner Daya Tariks - content_key kembali
        Content::updateOrCreate(
            ['content_key' => 'banner_daya_tariks'],
            ['content_value' => $this->dayaTariks, 'content_type' => 'json']
        );

        // Handle Banner Image Upload
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
        // ... (Logic for saveFooter remains the same as previous version) ...
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