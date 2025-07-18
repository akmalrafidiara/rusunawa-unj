<!DOCTYPE html>
<html>

    <head>
        <title>Daftar Tagihan</title>
        <style>
            /* Gaya dasar untuk PDF */
            body {
                font-family: sans-serif;
                font-size: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .header h1 {
                margin: 0;
                font-size: 18px;
            }

            .footer {
                text-align: center;
                margin-top: 50px;
                font-size: 8px;
                color: #777;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>Daftar Tagihan Rusunawa UNJ</h1>
            <p>Tanggal Export: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Penyewa</th>
                    <th>Unit</th>
                    <th>Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->contract->occupants->first()->full_name ?? '-' }}</td>
                        <td>{{ ($invoice->contract->unit->unitCluster->name ?? 'N/A') . ' | ' . ($invoice->contract->unit->room_number ?? 'N/A') }}
                        </td>
                        <td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td>{{ $invoice->due_at->translatedFormat('d M Y') }}</td>
                        <td>{{ $invoice->status->label() }}</td>
                        <td>{{ $invoice->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Daftar Tagihan Dihasilkan Oleh Sistem Rusunawa UNJ
        </div>
    </body>

</html>
