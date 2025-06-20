<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Location extends Component
{
    public $mainLocationTitle;    // Judul Lokasi Kami
    public $subLocationTitle;     // Subjudul Lokasi Kami
    public $locationAddress;      // Alamat
    public $locationEmbedLink;    // Link Embed Lokasi

    public $newNearbyLocation = ''; // Input untuk menambahkan lokasi terdekat baru
    public $nearbyLocations = [];   // Array untuk menyimpan daftar lokasi terdekat

    protected $rules = [
        'mainLocationTitle' => 'required|string|max:255',
        'subLocationTitle' => 'required|string|max:255',
        'locationAddress' => 'required|string|max:200',
        'locationEmbedLink' => 'nullable|string',
        'nearbyLocations' => 'array',
        'nearbyLocations.*' => 'required|string|max:100', // Validasi setiap item lokasi terdekat
        'newNearbyLocation' => 'nullable|string|max:100|min:3', // Untuk validasi input newNearbyLocation
    ];

    public function mount()
    {
        // Load existing content for all fields
        $this->mainLocationTitle = optional(Content::where('content_key', 'location_main_title')->first())->content_value ?? '';
        $this->subLocationTitle = optional(Content::where('content_key', 'location_sub_title')->first())->content_value ?? '';
        $this->locationAddress = optional(Content::where('content_key', 'location_address')->first())->content_value ?? '';
        $this->locationEmbedLink = optional(Content::where('content_key', 'location_embed_link')->first())->content_value ?? '';

        // Muat lokasi terdekat yang sudah ada. Defaults to empty array.
        // Content key kembali ke 'location_nearby_locations'
        $nearbyLocationsContent = optional(Content::where('content_key', 'location_nearby_locations')->first())->content_value;
        $this->nearbyLocations = is_array($nearbyLocationsContent) ? $nearbyLocationsContent : [];
    }

    public function addNearbyLocation() // Nama method disesuaikan
    {
        $this->validateOnly('newNearbyLocation'); // Validasi hanya input lokasi terdekat baru

        if (!empty($this->newNearbyLocation) && !in_array($this->newNearbyLocation, $this->nearbyLocations)) {
            $this->nearbyLocations[] = $this->newNearbyLocation;
            $this->newNearbyLocation = ''; // Reset input setelah ditambahkan
            sort($this->nearbyLocations); // Opsional: urutkan array
        } else if (!empty($this->newNearbyLocation)) {
            LivewireAlert::title('Lokasi terdekat sudah ada!') // Pesan alert disesuaikan
                ->warning()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function removeNearbyLocation($index) // Nama method disesuaikan
    {
        // Validasi indeks untuk mencegah error
        if (isset($this->nearbyLocations[$index])) {
            unset($this->nearbyLocations[$index]);
            $this->nearbyLocations = array_values($this->nearbyLocations); // Re-index array setelah penghapusan
        }
    }

    public function save()
    {
        $this->validate(); // Jalankan semua validasi

        // Update atau buat konten untuk setiap field
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

        // Simpan Lokasi Terdekat sebagai array JSON
        // Content key kembali ke 'location_nearby_locations'
        Content::updateOrCreate(
            ['content_key' => 'location_nearby_locations'],
            ['content_value' => $this->nearbyLocations, 'content_type' => 'json']
        );

        // Tampilkan alert sukses
        LivewireAlert::title('Konten Lokasi berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function render()
    {
        return view('livewire.managers.contents.locations.index');
    }
}