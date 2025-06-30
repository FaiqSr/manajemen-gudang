<!DOCTYPE html>
<html>

<head>
    <title>Laporan Hutang Usaha</title>
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
    <h2>Laporan Hutang Usaha</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>No. Invoice</th>
                <th>Tgl Pembelian</th>
                <th>Total Hutang</th>
                <th>Dibayar</th>
                <th>Sisa Hutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataHutang as $hutang)
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td>{{ $hutang->nama_supplier }}</td>
                    <td>{{ $hutang->nomor_invoice }}</td>
                    <td>{{ \Carbon\Carbon::parse($hutang->tanggal_pembelian)->format('d-m-Y') }}</td>
                    <td class="text-right">{{ number_format($hutang->total_biaya, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($hutang->total_dibayar, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($hutang->sisa_hutang, 0, ',', '.') }}</td>
            </tr>@empty<tr>
                    <td colspan="7" style="text-align:center">Tidak ada data hutang.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight:bold">
                <td colspan="6" class="text-right">Total Sisa Hutang</td>
                <td class="text-right">{{ number_format($dataHutang->sum('sisa_hutang'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
