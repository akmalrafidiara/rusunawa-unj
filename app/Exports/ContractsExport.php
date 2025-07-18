<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $contracts;

    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    public function collection()
    {
        return $this->contracts;
    }

    public function headings(): array
    {
        return [
            'Kode Kontrak',
            'Penyewa Utama',
            'Unit Cluster',
            'Nomor Kamar',
            'Tipe Penghuni',
            'Total Harga',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Dasar Harga',
            'Status',
            'Catatan',
        ];
    }

    public function map($contract): array
    {
        return [
            $contract->contract_code,
            $contract->occupants->first()->full_name ?? '-',
            $contract->unit->unitCluster->name ?? 'N/A',
            $contract->unit->room_number ?? 'N/A',
            $contract->occupantType->name ?? '-',
            $contract->total_price,
            $contract->start_date->format('Y-m-d'),
            $contract->end_date->format('Y-m-d'),
            $contract->pricing_basis->label(),
            $contract->status->label(),
            $contract->notes,
        ];
    }
}
