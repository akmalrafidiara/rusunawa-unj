<!DOCTYPE html>
<html>

    <head>
        <title>Selamat Datang di Rusunawa UNJ</title>
    </head>

    <body style="font-family: sans-serif; line-height: 1.6;">
        <div style="max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee;">
            <h2>Halo {{ $contract->pic->first()->full_name }},</h2>
            <p>
                Pemesanan Anda untuk unit <strong>{{ $contract->unit->room_number }}</strong> telah berhasil kami
                terima.
                Selamat datang di Rusunawa UNJ!
            </p>
            <hr>
            <h3>Opsi 1: Akses Cepat ke Dashboard</h3>
            <p>
                Klik tombol di bawah ini untuk masuk langsung ke dashboard Anda. Link ini hanya berlaku selama 24 jam
                dan hanya bisa digunakan satu kali.
            </p>
            <a href="{{ $url }}"
                style="display: inline-block; padding: 12px 25px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;">
                Masuk ke Dashboard Saya
            </a>
            <hr>
            <h3>Opsi 2: Login Manual (Simpan Informasi Ini)</h3>
            <p>
                Gunakan informasi berikut untuk login kapan saja melalui halaman login kami:
            </p>
            <ul>
                <li><strong>ID Pemesanan:</strong> {{ $contract->contract_code }}</li>
                <li><strong>Password:</strong> 5 digit terakhir nomor HP Anda yang terdaftar.</li>
            </ul>
            <p>
                Terima kasih!
            </p>
        </div>
    </body>

</html>
