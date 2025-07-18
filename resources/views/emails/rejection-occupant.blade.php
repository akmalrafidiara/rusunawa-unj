<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verifikasi Data Penghuni Rusunawa Ditolak</title>
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
                                <h1 style="font-size: 24px; color: #dc3545; margin-top: 0; font-weight: 600;">
                                    Verifikasi Data Anda Ditolak 😔
                                </h1>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Yth. Bapak/Ibu <strong>{{ $occupant->full_name }}</strong>,
                                </p>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Dengan hormat,
                                </p>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Kami memberitahukan bahwa permohonan verifikasi data hunian Anda di Rusunawa UNJ
                                    <strong>tidak dapat kami setujui</strong> untuk saat ini.
                                </p>

                                <h2
                                    style="font-size: 20px; color: #dc3545; border-top: 1px solid #eeeeee; padding-top: 25px; margin-top: 25px; font-weight: 600;">
                                    Alasan Penolakan
                                </h2>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    <strong>{{ $responseMessage }}</strong>
                                </p>
                                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">
                                    Mohon untuk segera melengkapi atau memperbaiki data yang diperlukan sesuai dengan
                                    informasi di atas. Anda dapat mengajukan permohonan verifikasi ulang setelah
                                    perbaikan dilakukan.
                                </p>

                                <p style="margin-top: 30px; font-size: 16px; color: #4a5568;">
                                    Jika Anda butuh bantuan atau ingin berdiskusi lebih lanjut, silakan balas email ini
                                    atau hubungi kami melalui saluran resmi.
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
                                <p style="margin: 0;">Anda menerima email ini karena ada pembaruan status terkait
                                    permohonan hunian Anda di Sistem Rusunawa UNJ.</p>
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
