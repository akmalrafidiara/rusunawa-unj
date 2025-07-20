<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Selamat Datang di Rusunawa UNJ</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f7f6;
            }

            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
            }

            .button {
                display: inline-block;
                padding: 14px 28px;
                background-color: #059669;
                /* Warna Aksen Anda */
                color: #ffffff !important;
                /* !important untuk memastikan warna teks putih */
                text-decoration: none;
                border-radius: 6px;
                font-weight: bold;
                font-size: 16px;
            }
        </style>
    </head>

    <body
        style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
            style="background-color: #f4f7f6; padding: 30px 0;">
            <tr>
                <td align="center">
                    <table class="container" width="100%" border="0" cellpadding="0" cellspacing="0"
                        role="presentation"
                        style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <tr>
                            <td
                                style="padding: 25px; padding-top: 50px; text-align: center; background-color: #ffffff;">
                                <img src="https://unj.ac.id/wp-content/uploads/2025/02/UNJ-LOGO-512-PX-1.png"
                                    alt="Logo UNJ" width="150">
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 30px 40px; color: #333333;">
                                <h1 style="font-size: 24px; color: #1a202c; margin-top: 0; font-weight: 600;">Selamat
                                    Datang, {{ $contract->pic->first()->full_name }}!</h1>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Pemesanan Anda untuk unit <strong
                                        style="font-size: 18px;">{{ $contract->unit->room_number }}</strong> telah
                                    berhasil dibuat.
                                </p>

                                @if ($invoice)
                                    <h2
                                        style="font-size: 20px; color: #059669; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                        Langkah Selanjutnya: Pembayaran</h2>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Pemesanan Anda telah kami konfirmasi. Silakan lakukan pembayaran sesuai dengan
                                        <strong>**invoice yang terlampir**</strong> pada email ini.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Anda juga dapat melihat detail tagihan dan melakukan konfirmasi pembayaran
                                        melalui <a href="{{ $url }}"
                                            style="color: #059669; text-decoration: none;">dashboard Anda</a>.
                                    </p>
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                        role="presentation" style="margin: 20px 0;">
                                        <tr>
                                            <td style="padding: 20px; background-color: #f9fafb; border-radius: 8px;">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                                    role="presentation">
                                                    <tr>
                                                        <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                            Nomor Virtual Account (Mandiri)</td>
                                                        <td
                                                            style="padding: 5px 0; font-size: 16px; color: #1a202c; text-align: right; font-weight: 600;">
                                                            <code
                                                                style="background-color: #e5e7eb; padding: 3px 6px; border-radius: 4px; letter-spacing: 2px;">
                                                                {{ chunk_split($contract->unit->virtual_account_number, 4, ' ') }}
                                                            </code>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                            Atas Nama</td>
                                                        <td
                                                            style="padding: 5px 0; font-size: 16px; color: #1a202c; text-align: right; font-weight: 600;">
                                                            Rusunawa UNJ
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                            Nominal Pembayaran</td>
                                                        <td
                                                            style="padding: 5px 0; font-size: 16px; color: #1a202c; text-align: right; font-weight: 600;">
                                                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td
                                                            style="padding: 10px 0 0 0; font-size: 15px; color: #4a5568; border-top: 1px solid #e2e8f0; margin-top: 10px;">
                                                            Batas Waktu Pembayaran</td>
                                                        <td
                                                            style="padding: 10px 0 0 0; font-size: 16px; color: #dc3545; text-align: right; font-weight: 600; border-top: 1px solid #e2e8f0; margin-top: 10px;">
                                                            {{ $invoice->due_at->format('d M Y, H:i') }} WIB
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <h2
                                        style="font-size: 20px; color: #059669; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                        Langkah Selanjutnya: Verifikasi Data</h2>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Saat ini, tim kami sedang melakukan verifikasi terhadap data Anda. Proses ini
                                        biasanya memakan waktu maksimal <strong>1x24 jam</strong>.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Anda akan menerima email selanjutnya yang berisi instruksi pembayaran setelah
                                        data Anda berhasil kami verifikasi.
                                    </p>
                                @endif

                                <h2
                                    style="font-size: 20px; color: #059669; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                    Akses Cepat ke Portal Anda</h2>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Gunakan tombol di bawah ini untuk masuk langsung ke dashboard Anda. Untuk keamanan,
                                    link ini akan kedaluwarsa dalam <strong>24 jam</strong>.
                                </p>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="margin: 25px 0;">
                                    <tr>
                                        <td align="center">
                                            {{-- Variabel di sini diubah menjadi $magicLink sesuai Mailable --}}
                                            <a href="{{ $url }}" class="button"
                                                style="display: inline-block; padding: 14px 28px; background-color: #059669; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                                Masuk ke Dashboard Saya
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <div
                                    style="background-color: #f9fafb; border-radius: 8px; padding: 20px; margin-top: 30px;">
                                    <h3 style="font-size: 18px; color: #1a202c; margin-top: 0; font-weight: 600;">Metode
                                        Login Manual</h3>
                                    <p style="font-size: 15px; color: #4a5568;">Simpan informasi ini untuk login kapan
                                        saja melalui <a href="{{ route('contract.auth') }}"
                                            style="color: #059669; text-decoration: none;">halaman portal</a> kami:</p>
                                    <ul style="list-style-type: none; padding-left: 0;">
                                        <li style="margin-bottom: 8px; font-size: 15px;"><strong>ID Pemesanan:</strong>
                                            <code
                                                style="background-color: #e5e7eb; padding: 3px 6px; border-radius: 4px; font-size: 18px; letter-spacing: 5px;">{{ $contract->contract_code }}</code>
                                        </li>
                                        <li style="font-size: 15px;"><strong>Password:</strong> 5 Digit Terakhir Nomor
                                            HP Anda</li>
                                    </ul>
                                </div>

                                <p style="margin-top: 30px; font-size: 16px; color: #4a5568;">
                                    Jika Anda butuh bantuan, silakan balas email ini.
                                    <br><br>
                                    Hormat kami,
                                    <br>
                                    **Tim Manajemen Rusunawa UNJ**
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td
                                style="padding: 20px 40px; text-align: center; color: #999999; font-size: 12px; background-color: #f9f9f9; border-top: 1px solid #eeeeee;">
                                <p style="margin: 0;">Anda menerima email ini karena melakukan pemesanan di Sistem
                                    Rusunawa UNJ.</p>
                                <p style="margin: 5px 0 0 0;">Rusunawa UNJ, Jl. Rawamangun Muka, Jakarta Timur, 13220
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>
