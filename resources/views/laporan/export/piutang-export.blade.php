<!DOCTYPE html>
<html>

<head>
    <title>Laporan Piutang Usaha</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px
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
            background-color: #f2f2f2;
            text-align: center
        }

        h2 {
            text-align: center
        }

        p {
            text-align: center;
            margin: 2px 0
        }

        .text-right {
            text-align: right
        }
    </style>
</head>

<body>
    <h2>Laporan Piutang Usaha</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>Tgl Penjualan</th>
                <th>Total Piutang</th>
                <th>Diterima</th>
                <th>Sisa Piutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataPiutang as $piutang)
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td>{{ $piutang->nama_pelanggan }}</td>
                    <td>{{ \Carbon\Carbon::parse($piutang->tanggal_penjualan)->format('d-m-Y') }}</td>
                    <td class="text-right">{{ number_format($piutang->total_pendapatan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($piutang->total_diterima, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($piutang->sisa_piutang, 0, ',', '.') }}</td>
            </tr>@empty<tr>
                    <td colspan="6" style="text-align:center">Tidak ada data piutang.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight:bold">
                <td colspan="5" class="text-right">Total Sisa Piutang</td>
                <td class="text-right">{{ number_format($dataPiutang->sum('sisa_piutang'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
