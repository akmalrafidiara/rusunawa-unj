<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Laporan Data Penghuni</title>
        <style>
            body {
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #333;
                line-height: 1.4;
                font-size: 12px;
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
                background-color: #f4f7f6;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 11px;
            }

            .header-table td {
                border: none;
                padding: 0;
            }

            .header-title {
                font-size: 24px;
                font-weight: bold;
                color: #059669;
                /* Warna Aksen Anda */
            }

            .text-right {
                text-align: right;
            }

            .page-break {
                page-break-after: always;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 10px;
                color: #777;
            }
        </style>
    </head>

    <body>
        <div class="footer">
            Laporan Data Penghuni - Rusunawa UNJ | Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
        </div>

        <table class="header-table">
            <tr>
                <td>
                    <h1 class="header-title">Laporan Data Penghuni</h1>
                    <p>Total Data: {{ count($occupants) }} Penghuni</p>
                </td>
                <td class="text-right">
                    <strong>Rusunawa UNJ</strong><br>
                    Jl. Rawamangun Muka<br>
                    Jakarta Timur, 13220
                </td>
            </tr>
        </table>

        <hr>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lengkap</th>
                    <th>Kontak</th>
                    <th>Status</th>
                    <th>Kontrak Aktif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($occupants as $index => $occupant)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $occupant['full_name'] }}</strong><br>
                            <small>{{ $occupant['email'] }}</small>
                        </td>
                        <td style="width: 20%;">{{ $occupant['whatsapp_number'] }}</td>
                        <td style="width: 15%;">{{ $occupant['status'] }}</td>
                        <td style="width: 35%;">{{ $occupant['contracts'] ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data penghuni yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </body>

</html>
