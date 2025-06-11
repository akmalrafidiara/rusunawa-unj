<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Gunakan ini untuk header yang lebih bersih
use Illuminate\Support\Collection;

// Ganti FromCollection menjadi WithHeadings untuk manajemen header yang lebih baik
class UnitsExport implements FromCollection, WithHeadings
{
    protected Collection $units;

    /**
     * Terima koleksi data yang sudah di-query sebelumnya.
     *
     * @param Collection $units
     */
    public function __construct(Collection $units)
    {
        $this->units = $units;
    }

    /**
     * Definisikan baris header untuk Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No Kamar',
            'Kapasitas',
            'No VA',
            'Peruntukan',
            'Status',
            'Unit Type',
            'Unit Cluster'
        ];
    }

    /**
     * Fungsi ini sekarang hanya memformat dan mengembalikan data yang sudah ada.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Query sudah tidak ada di sini. Langsung format data yang diterima.
        return $this->units->map(function ($unit) {
            return [
                'room_number' => $unit->room_number,
                'capacity' => $unit->capacity,
                'virtual_account_number' => (string) $unit->virtual_account_number,
                'gender_allowed' => $unit->gender_allowed->label(),
                'status' => $unit->status->label(),
                'unit_type_id' => $unit->unitType ? $unit->unitType->name : '',
                'unit_cluster_id' => $unit->unitCluster ? $unit->unitCluster->name : '',
            ];
        });
    }
}
