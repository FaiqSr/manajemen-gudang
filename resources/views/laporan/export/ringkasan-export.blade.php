<!DOCTYPE html>
<html>

<head>
    <title>Ringkasan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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

        .summary-table {
            width: 60%;
            margin: 0 auto 20px auto;
        }
    </style>
</head>

<body>
    <h2>Ringkasan Pendapatan vs Biaya Operasional</h2>
    <h4>
        Outlet: {{ $namaOutlet ?: 'Semua Outlet (Total)' }} <br>
        Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}
    </h4>

    <table class="summary-table">
        <tr>
            <td>Total Pendapatan</td>
            <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Biaya Operasional</td>
            <td class="text-right">(Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }})</td>
        </tr>
        <tr class="total">
            <td>SISA (PENDAPATAN - BIAYA)</td>
            <td class="text-right">Rp {{ number_format($totalPendapatan - $totalBiayaOperasional, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h4>Rincian Biaya Operasional</h4>
    <table>
        <thead>
            <tr>
                <th>Nama Biaya</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rincianBiaya as $item)
                <tr>
                    <td>{{ $item->nama_akun }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;"><em>Tidak ada biaya operasional.</em></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                <th>Total Biaya Operasional</th>
                <th class="text-right">Rp {{ number_format($rincianBiaya->sum('total'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
