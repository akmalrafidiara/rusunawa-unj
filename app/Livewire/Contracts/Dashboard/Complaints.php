<?php

namespace App\Livewire\Contracts\Dashboard;

use App\Enums\ReportStatus; 
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Complaints extends Component
{
    public $complaints;
    public $totalActiveComplaints = 0; 

    public function mount()
    {
        $contract = Auth::guard('contract')->user();

        if ($contract) {
            // Query untuk mengambil laporan yang statusnya BUKAN Selesai Dikonfirmasi
            $query = $contract->reports()
                ->where('status', '!=', ReportStatus::CONFIRMED_COMPLETED);

            // Hitung total pengaduan aktif sebelum di-limit
            $this->totalActiveComplaints = $query->count();

            // Ambil 3 laporan terbaru dan muat relasi yang dibutuhkan
            $this->complaints = $query->with(['reporter', 'attachments'])
                                      ->latest()
                                      ->take(3)
                                      ->get();
        } else {
            $this->complaints = collect();
        }
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.complaints');
    }
}
