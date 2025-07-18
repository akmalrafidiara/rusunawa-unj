<!DOCTYPE html>
<html>

    <head>
        <title>Daftar Kontrak</title>
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
            <h1>Daftar Kontrak Rusunawa UNJ</h1>
            <p>Tanggal Export: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Kode Kontrak</th>
                    <th>Penyewa Utama</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Mulai</th>
                    <th>Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contracts as $contract)
                    <tr>
                        <td>{{ $contract->contract_code }}</td>
                        <td>{{ $contract->occupants->first()->full_name ?? '-' }}</td>
                        <td>{{ ($contract->unit->unitCluster->name ?? 'N/A') . ' | ' . ($contract->unit->room_number ?? 'N/A') }}
                        </td>
                        <td>Rp {{ number_format($contract->total_price, 0, ',', '.') }}</td>
                        <td>{{ $contract->start_date->translatedFormat('d M Y') }}</td>
                        <td>{{ $contract->end_date->translatedFormat('d M Y') }}</td>
                        <td>{{ $contract->status->label() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Daftar Kontrak Dihasilkan Oleh Sistem Rusunawa UNJ
        </div>
    </body>

</html>
