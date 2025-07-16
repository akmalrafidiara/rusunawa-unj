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
                font-size: 14px;
            }

            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                font-size: 14px;
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

            .invoice-box table tr.top table td.title img {
                width: 80px;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 30px;
            }

            .invoice-box table tr.heading td {
                background: #f4f7f6;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 12px;
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

            .status {
                font-size: 1.1em;
                font-weight: bold;
                padding: 5px 15px;
                border-radius: 20px;
                display: inline-block;
            }

            .status.unpaid {
                color: #d9534f;
                background-color: #f2dede;
            }

            .status.paid {
                color: #059669;
                background-color: #dff0d8;
            }

            .footer {
                margin-top: 40px;
                font-size: 12px;
                text-align: center;
                color: #777;
            }
        </style>
    </head>

    <body>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                @php
                                    $base64 =
                                        'data:image/png;base64,' .
                                        base64_encode(file_get_contents(public_path('images/bpu-unj-logo.png')));
                                @endphp
                                <td class="title">
                                    <img src="{{ $base64 }}" alt="Logo UNJ" />
                                </td>
                                <td class="text-right">
                                    <h2 style="font-size: 28px; margin: 0; color: #333;">INVOICE</h2>
                                    <strong>#{{ $invoice->invoice_number }}</strong><br>
                                    <small>Dibuat: {{ $invoice->created_at->format('d M Y') }}</small><br>
                                    <small>Jatuh Tempo: {{ $invoice->due_at->format('d M Y') }}</small>
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
                                    <strong style="color: #555;">Ditagihkan Kepada:</strong><br>
                                    {{ $contract->pic->first()->full_name }}<br>
                                    {{ $contract->pic->first()->email }}
                                </td>
                                <td class="text-right">
                                    <strong style="color: #555;">Dari:</strong><br>
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
                    <td class="text-right">Jumlah</td>
                </tr>
                <tr class="item">
                    <td>
                        Pembayaran sewa untuk Unit <strong>{{ $contract->unit->room_number }}</strong><br>
                        <small>Periode: {{ \Carbon\Carbon::parse($contract->start_date)->translatedFormat('d F Y') }} -
                            {{ \Carbon\Carbon::parse($contract->end_date)->translatedFormat('d F Y') }}</small>
                    </td>
                    <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td class="text-right" style="font-weight: bold; font-size: 1.2em;">Total</td>
                    <td class="text-right" style="font-weight: bold; font-size: 1.2em;">
                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 40px;">
                        @if ($invoice->status == 'paid')
                            <span class="status paid">LUNAS</span>
                        @else
                            <span class="status unpaid">BELUM DIBAYAR</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="footer">
                        <p>Silakan lakukan pembayaran ke Nomor Virtual Account di bawah ini dan lakukan konfirmasi
                            pembayaran di dashboard Anda.</p>
                        <p>
                            <strong>Bank Mandiri: {{ $contract->unit->virtual_account_number }}</strong><br>
                            <small>(a.n. Rusunawa Universitas Negeri Jakarta)</small>
                        </p>
                        <hr style="border: none; border-top: 1px solid #eee; margin-top: 20px;">
                        <p>Terima kasih atas perhatian Anda.</p>
                    </td>
                </tr>
            </table>
        </div>
    </body>

</html>
