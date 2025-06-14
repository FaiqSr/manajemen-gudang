<!DOCTYPE html>
<html>

<head>
    <title>Laporan Arus Kas</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .header {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        h2,
        h4 {
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h2>Laporan Arus Kas</h2>
    <h4>
        Outlet: {{ $namaOutlet ?: 'Semua Outlet (Total)' }} <br>
        Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
    </h4>
    <br>

    <table>
        <tr>
            <th>Saldo Kas Awal Periode</th>
            <th class="text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</th>
        </tr>

        <tr class="header">
            <th colspan="2">Arus Kas dari Aktivitas Operasi</th>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Penerimaan dari Pelanggan</td>
            <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_operasi'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Pembayaran ke Supplier & Beban Operasional</td>
            <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_operasi'], 0, ',', '.') }})</td>
        </tr>
        @php $net_operasi = $laporan['totals']['masuk_operasi'] - $laporan['totals']['keluar_operasi']; @endphp
        <tr class="total">
            <th style="padding-left: 20px;">Arus Kas Bersih dari Aktivitas Operasi</th>
            <th class="text-right">Rp {{ number_format($net_operasi, 0, ',', '.') }}</th>
        </tr>

        <tr class="header">
            <th colspan="2">Arus Kas dari Aktivitas Investasi</th>
        </tr>
        @php $net_investasi = $laporan['totals']['masuk_investasi'] - $laporan['totals']['keluar_investasi']; @endphp
        <tr>
            <td style="padding-left: 20px;">Pembelian Aset Tetap</td>
            <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_investasi'], 0, ',', '.') }})</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Penjualan Aset Tetap</td>
            <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_investasi'], 0, ',', '.') }}</td>
        </tr>
        <tr class="total">
            <th style="padding-left: 20px;">Arus Kas Bersih dari Aktivitas Investasi</th>
            <th class="text-right">Rp {{ number_format($net_investasi, 0, ',', '.') }}</th>
        </tr>

        <tr class="header">
            <th colspan="2">Arus Kas dari Aktivitas Pendanaan</th>
        </tr>
        @php $net_pendanaan = $laporan['totals']['masuk_pendanaan'] - $laporan['totals']['keluar_pendanaan']; @endphp
        <tr>
            <td style="padding-left: 20px;">Setoran Modal / Penerimaan Pinjaman</td>
            <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_pendanaan'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="padding-left: 20px;">Pembayaran Utang Bank</td>
            <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_pendanaan'], 0, ',', '.') }})</td>
        </tr>
        <tr class="total">
            <th style="padding-left: 20px;">Arus Kas Bersih dari Aktivitas Pendanaan</th>
            <th class="text-right">Rp {{ number_format($net_pendanaan, 0, ',', '.') }}</th>
        </tr>

        @php $kenaikan_bersih = $net_operasi + $net_investasi + $net_pendanaan; @endphp
        <tr class="total">
            <th>Kenaikan (Penurunan) Bersih Kas</th>
            <th class="text-right">Rp {{ number_format($kenaikan_bersih, 0, ',', '.') }}</th>
        </tr>
        @php $saldo_akhir = $saldoAwal + $kenaikan_bersih; @endphp
        <tr class="header">
            <th>SALDO KAS AKHIR PERIODE</th>
            <th class="text-right">Rp {{ number_format($saldo_akhir, 0, ',', '.') }}</th>
        </tr>
    </table>
</body>

</html>
