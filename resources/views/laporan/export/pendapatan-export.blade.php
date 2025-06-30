<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px
        }

        th {
            background-color: #f2f2f2
        }

        h2,
        h4 {
            text-align: center;
            margin: 2px 0
        }

        .text-right {
            text-align: right
        }
    </style>
</head>

<body>
    <h2>Laporan Pendapatan</h2>
    <h4>Outlet: {{ $namaOutlet }} | Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Sumber Pendapatan</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanPendapatan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_akun }}</td>
                    <td class="text-right">{{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
            </tr>@empty<tr>
                    <td colspan="3" style="text-align:center">Tidak ada data pendapatan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight:bold">
                <td colspan="2" class="text-right">Total Pendapatan</td>
                <td class="text-right">{{ number_format($laporanPendapatan->sum('total_pendapatan'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
