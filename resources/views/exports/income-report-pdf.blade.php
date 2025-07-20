<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Laporan Pendapatan</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                font-size: 12px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
            }

            .header h1 {
                margin: 0;
                font-size: 24px;
                color: #333;
            }

            .header p {
                margin: 5px 0;
                color: #666;
            }

            .summary {
                background: #f8f9fa;
                padding: 15px;
                border: 1px solid #ddd;
                margin-bottom: 20px;
                border-radius: 5px;
            }

            .summary h3 {
                margin: 0 0 10px 0;
                color: #333;
            }

            .summary-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }

            .summary-item {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #eee;
            }

            .summary-item:last-child {
                border-bottom: none;
            }

            .summary-label {
                font-weight: bold;
                color: #555;
            }

            .summary-value {
                color: #333;
            }

            .total-amount {
                font-size: 18px;
                font-weight: bold;
                color: #10b981;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f8f9fa;
                font-weight: bold;
                color: #333;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .amount {
                text-align: right;
                font-weight: bold;
                color: #10b981;
            }

            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 10px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }

            .no-data {
                text-align: center;
                padding: 40px;
                color: #666;
                font-style: italic;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>LAPORAN PENDAPATAN</h1>
            <p>Rusunawa UNJ - Universitas Negeri Jakarta</p>
            <p>{{ now()->format('d F Y') }}</p>
        </div>

        <div class="summary">
            <h3>Ringkasan Laporan</h3>
            <div class="summary-grid">
                <div>
                    <div class="summary-item">
                        <span class="summary-label">Total Pendapatan:</span>
                        <span class="summary-value total-amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Jumlah Transaksi:</span>
                        <span class="summary-value">{{ $invoices->count() }} transaksi</span>
                    </div>
                </div>
                <div>
                    <div class="summary-item">
                        <span class="summary-label">Periode:</span>
                        <span class="summary-value">
                            @if ($invoices->isNotEmpty())
                                {{ $invoices->min('paid_at') ? \Carbon\Carbon::parse($invoices->min('paid_at'))->format('d/m/Y') : '-' }}
                                -
                                {{ $invoices->max('paid_at') ? \Carbon\Carbon::parse($invoices->max('paid_at'))->format('d/m/Y') : '-' }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Rata-rata per Transaksi:</span>
                        <span class="summary-value">Rp
                            {{ $invoices->count() > 0 ? number_format($totalIncome / $invoices->count(), 0, ',', '.') : '0' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($invoices->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Nomor Invoice</th>
                        <th style="width: 12%">Kode Kontrak</th>
                        <th style="width: 20%">Nama Penghuni</th>
                        <th style="width: 20%">Deskripsi</th>
                        <th style="width: 15%">Jumlah</th>
                        <th style="width: 13%">Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $index => $invoice)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->contract->contract_code ?? '-' }}</td>
                            <td>{{ $invoice->contract->occupants->pluck('full_name')->join(', ') ?? '-' }}</td>
                            <td>{{ $invoice->description }}</td>
                            <td class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                            <td>{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #e8f5e8; font-weight: bold;">
                        <td colspan="5">TOTAL PENDAPATAN</td>
                        <td class="amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-data">
                <p>Tidak ada data pendapatan untuk periode yang dipilih.</p>
            </div>
        @endif

        <div class="footer">
            <p>Laporan ini dibuat secara otomatis oleh sistem Rusunawa UNJ pada {{ now()->format('d F Y H:i:s') }}</p>
        </div>
    </body>

</html>
