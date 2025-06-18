<?php

namespace App\Livewire\Frontend;

use App\Models\UnitType;
use Livewire\Component;

class ShowAvailableUnitTypes extends Component
{
    public $filters = [];

    public function mount()
    {
        // Ambil filter dari URL saat halaman pertama kali dimuat
        $this->filters = [
            'occupantType' => request()->query('occupantType'),
            'pricingBasis' => request()->query('pricingBasis'),
            'startDate' => request()->query('startDate'),
            'endDate' => request()->query('endDate'),
        ];
    }
    
    public function render()
    {
        // Ambil data Tipe Unit berdasarkan filter
        $query = UnitType::query()
            ->whereHas('units', function ($unitQuery) {
                // Filter unit yang statusnya 'available'
                $unitQuery->where('status', 'available')
                    // DAN unit yang memiliki rate sesuai filter
                    ->whereHas('rates', function ($rateQuery) {
                        if (!empty($this->filters['occupantType'])) {
                            $rateQuery->where('occupant_type', $this->filters['occupantType']);
                        }
                        if (!empty($this->filters['pricingBasis'])) {
                            $rateQuery->where('pricing_basis', $this->filters['pricingBasis']);
                        }
                    });
                
                // Tambahkan logika pengecekan ketersediaan tanggal (booking) di sini jika diperlukan
                // Ini adalah contoh sederhana, logika booking bisa sangat kompleks
            });

        return view('livewire.frontend.show-available-unit-types', [
            'unitTypes' => $query->get() // Ambil 6 tipe unit per halaman
        ]);
    }

    public function applyFilters($newFilters)
    {
        $this->filters = $newFilters;
        $this->resetPage(); // Reset paginasi ke halaman 1
    }
}
