<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <title>Data Unit Rusunawa</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, sans-serif;
                font-size: 12px;
                margin: 40px;
                color: #000;
            }

            .kop-surat {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                margin-bottom: 10px;
            }

            .kop-surat img {
                width: 100px;
                height: auto;
            }

            .kop-surat .instansi {
                text-align: center;
                flex: 1;
            }

            .kop-surat .instansi h1 {
                font-size: 18px;
                margin: 0;
                text-transform: uppercase;
            }

            .kop-surat .instansi p {
                font-size: 11px;
                margin: 2px 0;
            }

            .garis-bawah {
                border-bottom: 2px solid #000;
                margin-top: 4px;
                margin-bottom: 20px;
            }

            h2 {
                text-align: center;
                margin-bottom: 10px;
                font-size: 16px;
                font-weight: bold;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
            }

            th,
            td {
                border: 1px solid #666;
                padding: 6px 8px;
                text-align: left;
            }

            th {
                background-color: #eee;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            td {
                text-transform: capitalize;
            }

            footer {
                position: fixed;
                bottom: 10px;
                left: 0;
                right: 0;
                font-size: 10px;
                color: #aaa;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <!-- Kop Surat -->
        @php
            $base64 =
                'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/bpu-unj-logo.png')));
        @endphp
        <div class="kop-surat">
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="{{ $base64 }}" alt="Logo">
            </div>
            <div class="instansi">
                <h1>Badan Pengelola Usaha</h1>
                <h1>Universitas Negeri Jakarta</h1>
                <p>Rusunawa Mahasiswa</p>
                <p>Jl. Rawamangun Muka, Jakarta Timur, DKI Jakarta 13220</p>
                <p>Telepon: (021) 4898486 | Email: rusunawa@unj.ac.id</p>
            </div>
        </div>
        <div class="garis-bawah"></div>

        <!-- Judul -->
        <h2>Data Unit Rusunawa</h2>

        <!-- Tabel Data -->
        <table>
            <thead>
                <tr>
                    <th>No Kamar</th>
                    <th>Kapasitas</th>
                    <th>No VA</th>
                    <th>Peruntukan</th>
                    <th>Status</th>
                    <th>Tipe Unit</th>
                    <th>Cluster Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $unit)
                    <tr>
                        <td>{{ $unit['room_number'] }}</td>
                        <td>{{ $unit['capacity'] }}</td>
                        <td>
                            <pre>{{ $unit['virtual_account_number'] }}</pre>
                        </td>
                        <td>{{ $unit['gender_allowed'] }}</td>
                        <td>{{ $unit['status'] }}</td>
                        <td>{{ $unit['unit_type_id'] }}</td>
                        <td>{{ $unit['unit_cluster_id'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <footer
            style="position: fixed; bottom: 10px; left: 0; right: 0; font-size: 10px; color: #aaa; text-align: center;">
            Dokumen ini dibuat otomatis oleh Sistem Informasi Management Rusunawa UNJ <br /> Dicetak pada
            {{ date('d/m/Y H:i:s') }}
        </footer>

    </body>

</html>
