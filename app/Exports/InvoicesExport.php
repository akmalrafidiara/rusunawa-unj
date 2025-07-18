<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Penyewa',
            'Unit',
            'Jumlah',
            'Tanggal Jatuh Tempo',
            'Tanggal Pembayaran',
            'Status',
            'Deskripsi',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->contract->occupants->first()->full_name ?? '-',
            ($invoice->contract->unit->unitCluster->name ?? 'N/A') . ' | ' . ($invoice->contract->unit->room_number ?? 'N/A'),
            $invoice->amount,
            $invoice->due_at->format('Y-m-d'),
            $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '-',
            $invoice->status->label(),
            $invoice->description,
        ];
    }
}
