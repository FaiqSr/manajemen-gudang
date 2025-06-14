<!DOCTYPE html>
<html>

<head>
    <title>Laporan Laba Rugi</title>
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
    <h2>Laporan Laba Rugi</h2>
    <h4>
        Outlet: {{ $namaOutlet ?: 'Semua Outlet (Total)' }} <br>
        Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
    </h4>
    <br>

    <table>
        <tr class="header">
            <th colspan="2">PENDAPATAN</th>
        </tr>
        @forelse($laporan['Pendapatan'] as $nama_akun => $total)
            <tr>
                <td style="padding-left: 20px;">{{ $nama_akun }}</td>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="padding-left: 20px;"><em>Tidak ada pendapatan</em></td>
            </tr>
        @endforelse
        <tr class="total">
            <th class="text-right">TOTAL PENDAPATAN</th>
            <th class="text-right">{{ number_format($laporan['totals']['pendapatan'], 0, ',', '.') }}</th>
        </tr>

        <tr>
            <td colspan="2" style="border: none;">&nbsp;</td>
        </tr>

        <tr class="header">
            <th colspan="2">BEBAN POKOK PENJUALAN</th>
        </tr>
        @forelse($laporan['Beban Pokok Penjualan'] as $nama_akun => $total)
            <tr>
                <td style="padding-left: 20px;">{{ $nama_akun }}</td>
                <td class="text-right">({{ number_format($total, 0, ',', '.') }})</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="padding-left: 20px;"><em>Tidak ada HPP</em></td>
            </tr>
        @endforelse
        <tr class="header">
            <th class="text-right">LABA KOTOR</th>
            <th class="text-right">{{ number_format($laporan['totals']['laba_kotor'], 0, ',', '.') }}</th>
        </tr>

        <tr>
            <td colspan="2" style="border: none;">&nbsp;</td>
        </tr>

        <tr class="header">
            <th colspan="2">BEBAN OPERASIONAL</th>
        </tr>
        @forelse($laporan['Beban Operasional'] as $nama_akun => $total)
            <tr>
                <td style="padding-left: 20px;">{{ $nama_akun }}</td>
                <td class="text-right">({{ number_format($total, 0, ',', '.') }})</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="padding-left: 20px;"><em>Tidak ada beban operasional</em></td>
            </tr>
        @endforelse
        <tr class="total">
            <th class="text-right">TOTAL BEBAN OPERASIONAL</th>
            <th class="text-right">({{ number_format($laporan['totals']['operasional'], 0, ',', '.') }})</th>
        </tr>

        <tr>
            <td colspan="2" style="border: none;">&nbsp;</td>
        </tr>

        <tr class="header">
            <th class="text-right">LABA BERSIH</th>
            <th class="text-right">{{ number_format($laporan['totals']['laba_bersih'], 0, ',', '.') }}</th>
        </tr>
    </table>
</body>

</html>
