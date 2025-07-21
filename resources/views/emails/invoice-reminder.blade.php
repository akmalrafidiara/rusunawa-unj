<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            @if ($reminderType === 'due_soon')
                Pengingat: Tagihan Akan Jatuh Tempo - #{{ $invoice->invoice_number }}
            @elseif ($reminderType === 'overdue')
                Peringatan: Tagihan Sudah Jatuh Tempo! - #{{ $invoice->invoice_number }}
            @elseif ($reminderType === 'created')
                Pemberitahuan: Tagihan Rusunawa UNJ Anda Dibuat! - #{{ $invoice->invoice_number }}
            @endif
        </title>
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
                                @if ($reminderType === 'due_soon')
                                    <h1 style="font-size: 24px; color: #eab308; margin-top: 0; font-weight: 600;">
                                        Pengingat: Tagihan Anda Akan Jatuh Tempo!
                                    </h1>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Halo {{ $occupantName }}, ini adalah pengingat mengenai tagihan sewa unit
                                        <strong style="font-size: 18px;">{{ $unitCluster }} |
                                            {{ $unitNumber }}</strong> Anda.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Tagihan ini akan jatuh tempo pada tanggal <strong
                                            style="color: #eab308;">{{ $invoice->due_at->format('d F Y H:i') }}
                                            WIB</strong>.
                                        Mohon segera lakukan pembayaran untuk menghindari denda atau sanksi.
                                    </p>
                                @elseif ($reminderType === 'overdue')
                                    <h1 style="font-size: 24px; color: #ef4444; margin-top: 0; font-weight: 600;">
                                        Peringatan: Tagihan Anda Sudah Jatuh Tempo!
                                    </h1>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Halo {{ $occupantName }}, ini adalah peringatan penting mengenai tagihan sewa
                                        unit
                                        <strong style="font-size: 18px;">{{ $unitCluster }} |
                                            {{ $unitNumber }}</strong> Anda.
                                    </p>
                                    <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                        Tagihan ini sudah jatuh tempo pada tanggal <strong
                                            style="color: #ef4444;">{{ $invoice->due_at->format('d F Y H:i') }}
                                            WIB</strong>.
                                        Mohon segera lakukan pembayaran untuk menghindari denda atau sanksi lebih
                                        lanjut.
                                    </p>
                                @endif

                                <h2
                                    style="font-size: 20px; color: #059669; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                    Detail Tagihan</h2>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="margin: 20px 0;">
                                    <tr>
                                        <td style="padding: 20px; background-color: #f9fafb; border-radius: 8px;">
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0"
                                                role="presentation">
                                                <tr>
                                                    <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                        Nomor Invoice</td>
                                                    <td
                                                        style="padding: 5px 0; font-size: 16px; color: #1a202c; text-align: right; font-weight: 600;">
                                                        #{{ $invoice->invoice_number }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                        Deskripsi</td>
                                                    <td
                                                        style="padding: 5px 0; font-size: 16px; color: #1a202c; text-align: right; font-weight: 600;">
                                                        {{ $invoice->description }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0; font-size: 15px; color: #4a5568;">
                                                        Jumlah Pembayaran</td>
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

                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Untuk melihat detail tagihan lengkap dan mengelola pembayaran, silakan masuk ke
                                    dashboard Anda:
                                </p>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation"
                                    style="margin: 25px 0;">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ url('/occupant/dashboard') }}" class="button"
                                                style="display: inline-block; padding: 14px 28px; background-color: #059669; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                                                Lihat Tagihan Saya
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin-top: 30px; font-size: 16px; color: #4a5568;">
                                    Jika Anda butuh bantuan, silakan balas email ini.
                                    <br><br>
                                    Hormat kami,
                                    <br>
                                    <strong>Tim Manajemen Rusunawa UNJ</strong>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td
                                style="padding: 20px 40px; text-align: center; color: #999999; font-size: 12px; background-color: #f9f9f9; border-top: 1px solid #eeeeee;">
                                <p style="margin: 0;">Anda menerima email ini karena terkait dengan tagihan di Sistem
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
