<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Invoice #{{ $invoice->invoice_number }}</title>
        <style>
            body {
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #333;
                line-height: 1.5;
            }

            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                font-size: 16px;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
                border-collapse: collapse;
            }

            .invoice-box table td {
                padding: 8px;
                vertical-align: top;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 45px;
                line-height: 45px;
                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td {
                border-bottom: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            .text-right {
                text-align: right;
            }

            .text-left {
                text-align: left;
            }

            .status {
                font-size: 20px;
                font-weight: bold;
                padding: 5px 10px;
                border-radius: 5px;
            }

            .status.unpaid {
                color: #d9534f;
                border: 1px solid #d9534f;
            }

            .status.paid {
                color: #5cb85c;
                border: 1px solid #5cb85c;
            }
        </style>
    </head>

    <body>
        <div class="invoice-box">
            <table>
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    {{-- GANTI DENGAN URL LOGO ANDA --}}
                                    <img src="{{-- public_path('path/to/your/logo.png') --}}" style="width: 100%; max-width: 150px"
                                        alt="Logo Rusunawa" />
                                </td>
                                <td class="text-right">
                                    <strong>INVOICE #{{ $invoice->invoice_number }}</strong><br>
                                    Dibuat: {{ $invoice->created_at->format('d M Y') }}<br>
                                    Jatuh Tempo: {{ $invoice->due_date->format('d M Y') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    <strong>Penerima:</strong><br>
                                    {{ $contract->pic->first()->full_name }}<br>
                                    {{ $contract->pic->first()->email }}<br>
                                    {{ $contract->pic->first()->whatsapp_number }}
                                </td>
                                <td class="text-right">
                                    <strong>Ditagihkan Oleh:</strong><br>
                                    Manajemen Rusunawa UNJ<br>
                                    Jl. Rawamangun Muka<br>
                                    Jakarta Timur, 13220
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="heading">
                    <td>Deskripsi</td>
                    <td class="text-right">Harga</td>
                </tr>
                <tr class="item">
                    <td>
                        Sewa Unit <strong>{{ $contract->unit->room_number }}</strong><br>
                        <small>Periode: {{ $contract->start_date->format('d M Y') }} -
                            {{ $contract->end_date->format('d M Y') }}</small>
                    </td>
                    <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td></td>
                    <td class="text-right">
                        <strong>Total: Rp {{ number_format($invoice->amount, 0, ',', '.') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 40px;">
                        <strong>Status:</strong>
                        @if ($invoice->status === 'paid')
                            <span class="status paid">LUNAS</span>
                        @else
                            <span class="status unpaid">BELUM DIBAYAR</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 40px; font-size: 14px; text-align: center; color: #777;">
                        <p>Silakan lakukan pembayaran ke rekening berikut dan lakukan konfirmasi pembayaran di dashboard
                            Anda.</p>
                        <p><strong>Bank Mandiri: 123-456-7890 (a.n. Rusunawa UNJ)</strong></p>
                        <hr>
                        <p>Terima kasih atas pembayaran Anda.</p>
                    </td>
                </tr>
            </table>
        </div>
    </body>

</html>
