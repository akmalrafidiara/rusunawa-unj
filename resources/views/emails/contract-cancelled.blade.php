<!DOCTYPE html>
<html lang="id">

    <head>
        <title>Pemesanan Dibatalkan</title>
    </head>

    <body style="font-family: sans-serif; background-color: #f4f7f6; padding: 20px;">
        <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 30px;">
            <h1 style="color: #dc3545;">Pemesanan Anda Dibatalkan</h1>
            <p style="color: #4a5568;">Halo {{ $contract->pic->full_name }},</p>
            <p style="color: #4a5568;">
                Dengan berat hati kami memberitahukan bahwa pemesanan Anda untuk unit
                <strong>{{ $contract->unit->room_number }}</strong> dengan ID Pemesanan
                <strong>{{ $contract->contract_code }}</strong> telah dibatalkan secara otomatis.
            </p>
            <p style="color: #4a5568;">
                Pembatalan ini dilakukan karena pembayaran tidak diterima sebelum batas waktu yang ditentukan.
            </p>
            <p style="color: #4a5568;">
                Jika ini adalah sebuah kesalahan atau jika Anda ingin melakukan pemesanan kembali, silakan kunjungi
                website kami. Unit tersebut kini tersedia kembali untuk dipesan oleh penghuni lain.
            </p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('tenancy.index') }}"
                    style="background-color: #059669; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Cari Unit Lain
                </a>
            </div>
            <p style="color: #4a5568;">Terima kasih atas pengertian Anda.</p>
        </div>
    </body>

</html>
