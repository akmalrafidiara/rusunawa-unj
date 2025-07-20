<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
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
            'No',
            'Nomor Invoice',
            'Kode Kontrak',
            'Nama Penghuni',
            'Deskripsi',
            'Jumlah (Rp)',
            'Tanggal Jatuh Tempo',
            'Tanggal Pembayaran',
            'Status'
        ];
    }

    public function map($invoice): array
    {
        static $no = 1;

        return [
            $no++,
            $invoice->invoice_number,
            $invoice->contract->contract_code ?? '-',
            $invoice->contract->occupants->pluck('full_name')->join(', ') ?? '-',
            $invoice->description,
            number_format($invoice->amount, 0, ',', '.'),
            $invoice->due_at ? Carbon::parse($invoice->due_at)->format('d/m/Y') : '-',
            $invoice->paid_at ? Carbon::parse($invoice->paid_at)->format('d/m/Y H:i') : '-',
            $invoice->status->label()
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }
}
