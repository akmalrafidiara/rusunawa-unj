<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Location extends Component
{
    public $mainLocationTitle;
    public $subLocationTitle;     
    public $locationAddress;      
    public $locationEmbedLink;    
    public $newNearbyLocation = ''; 
    public $nearbyLocations = [];  

    protected $rules = [
        'mainLocationTitle' => 'required|string|max:255',
        'subLocationTitle' => 'required|string|max:255',
        'locationAddress' => 'required|string|max:200',
        'locationEmbedLink' => 'required|string',
        'nearbyLocations' => 'array',
        'nearbyLocations.*' => 'required|string|max:100',
        'newNearbyLocation' => 'nullable|string|max:100|min:3',
    ];

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'mainLocationTitle.required' => 'Kolom Judul Lokasi Kami wajib diisi.',
        'mainLocationTitle.string' => 'Judul Lokasi Kami harus berupa teks.',
        'mainLocationTitle.max' => 'Judul Lokasi Kami tidak boleh lebih dari :max karakter.',

        'subLocationTitle.required' => 'Kolom Subjudul Lokasi Kami wajib diisi.',
        'subLocationTitle.string' => 'Subjudul Lokasi Kami harus berupa teks.',
        'subLocationTitle.max' => 'Subjudul Lokasi Kami tidak boleh lebih dari :max karakter.',

        'locationAddress.required' => 'Kolom Alamat wajib diisi.',
        'locationAddress.string' => 'Alamat harus berupa teks.',
        'locationAddress.max' => 'Alamat tidak boleh lebih dari :max karakter.',

        'locationEmbedLink.required' => 'Kolom Link Embed Lokasi wajib diisi.',
        'locationEmbedLink.string' => 'Link Embed Lokasi harus berupa teks.',

        'nearbyLocations.array' => 'Daftar Lokasi Terdekat tidak valid.',
        'nearbyLocations.*.required' => 'Lokasi terdekat tidak boleh kosong.',
        'nearbyLocations.*.string' => 'Lokasi terdekat harus berupa teks.',
        'nearbyLocations.*.max' => 'Lokasi terdekat tidak boleh lebih dari :max karakter.',

        'newNearbyLocation.required' => 'Lokasi terdekat baru wajib diisi.',
        'newNearbyLocation.string' => 'Lokasi terdekat baru harus berupa teks.',
        'newNearbyLocation.max' => 'Lokasi terdekat baru tidak boleh lebih dari :max karakter.',
        'newNearbyLocation.min' => 'Lokasi terdekat baru minimal harus :min karakter.',
    ];


    public function mount()
    {
        $this->mainLocationTitle = optional(Content::where('content_key', 'location_main_title')->first())->content_value ?? '';
        $this->subLocationTitle = optional(Content::where('content_key', 'location_sub_title')->first())->content_value ?? '';
        $this->locationAddress = optional(Content::where('content_key', 'location_address')->first())->content_value ?? '';
        $this->locationEmbedLink = optional(Content::where('content_key', 'location_embed_link')->first())->content_value ?? '';

        $nearbyLocationsContent = optional(Content::where('content_key', 'location_nearby_locations')->first())->content_value;
        $this->nearbyLocations = is_array($nearbyLocationsContent) ? $nearbyLocationsContent : [];
    }

    public function render()
    {
        return view('livewire.managers.contents.locations.index');
    }

    public function addNearbyLocation()
    {
        $this->validateOnly('newNearbyLocation');

        $normalizedNewLocation = trim(strtolower($this->newNearbyLocation));
        $normalizedExistingLocations = array_map('strtolower', $this->nearbyLocations);

        if (!empty($this->newNearbyLocation) && !in_array($normalizedNewLocation, $normalizedExistingLocations)) {
            $this->nearbyLocations[] = trim($this->newNearbyLocation);
            $this->newNearbyLocation = '';

            LivewireAlert::title('Lokasi terdekat berhasil ditambahkan!')
                ->success()
                ->text('Jangan lupa klik tombol "Update" untuk menyimpan perubahan ke database.')
                ->toast()
                ->position('top-end')
                ->show();
        } else if (!empty($this->newNearbyLocation)) {
            LivewireAlert::title('Lokasi terdekat sudah ada dalam daftar!')
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function removeNearbyLocation($index)
    {
        if (isset($this->nearbyLocations[$index])) {
            unset($this->nearbyLocations[$index]);
            $this->nearbyLocations = array_values($this->nearbyLocations);

            LivewireAlert::title('Lokasi terdekat berhasil dihapus!')
                ->info()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function save()
    {
        $this->validate();

        Content::updateOrCreate(
            ['content_key' => 'location_main_title'],
            ['content_value' => $this->mainLocationTitle, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_sub_title'],
            ['content_value' => $this->subLocationTitle, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_address'],
            ['content_value' => $this->locationAddress, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_embed_link'],
            ['content_value' => $this->locationEmbedLink, 'content_type' => 'html_embed']
        );

        Content::updateOrCreate(
            ['content_key' => 'location_nearby_locations'],
            ['content_value' => $this->nearbyLocations, 'content_type' => 'json']
        );

        LivewireAlert::title('Konten Lokasi berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }
}