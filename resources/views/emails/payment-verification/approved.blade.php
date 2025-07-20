<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verifikasi Pembayaran Disetujui</title>
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
                                {{-- Gunakan logo yang relevan dengan aplikasi Anda, atau biarkan URL contoh ini --}}
                                <img src="https://unj.ac.id/wp-content/uploads/2025/02/UNJ-LOGO-512-PX-1.png"
                                    alt="Logo UNJ" width="150">
                            </td>
                        </tr>

                        <tr>
                            <td style="padding: 30px 40px; color: #333333;">
                                <h1 style="font-size: 24px; color: #059669; margin-top: 0; font-weight: 600;">
                                    Selamat, Verifikasi Pembayaran Anda Telah Disetujui! 🎉
                                </h1>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Halo <strong>{{ $payment->invoice->contract->pic->full_name }}</strong>,
                                </p>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Pembayaran Anda sebesar Rp{{ number_format($payment->amount_paid, 0, ',', '.') }}
                                    untuk
                                    Invoice
                                    #{{ $payment->invoice->invoice_number }} telah berhasil diverifikasi dan disetujui.
                                </p>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Terima kasih atas pembayaran tepat waktu Anda.
                                </p>

                                <h2
                                    style="font-size: 20px; color: #059669; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                    Detail Verifikasi
                                </h2>
                                <ul style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    <li>Status: Disetujui</li>
                                    <li>Jumlah Pembayaran: Rp{{ number_format($payment->amount_paid, 0, ',', '.') }}
                                    </li>
                                    <li>Tanggal Verifikasi: {{ $verificationLog->processed_at->format('d M Y H:i') }}
                                    </li>
                                    <li>Catatan dari Admin: {{ $verificationLog->reason ?? 'Tidak ada catatan.' }}</li>
                                </ul>

                                <p style="margin-top: 30px; font-size: 16px; color: #4a5568;">
                                    Hormat kami,
                                    <br>
                                    <strong>Manajemen Rusunawa UNJ</strong>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td
                                style="padding: 20px 40px; text-align: center; color: #999999; font-size: 12px; background-color: #f9f9f9; border-top: 1px solid #eeeeee;">
                                <p style="margin: 0;">Anda menerima email ini karena ada pembaruan status terkait
                                    pembayaran Anda di Sistem Rusunawa UNJ.</p>
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
