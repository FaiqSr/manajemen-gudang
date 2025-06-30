<!DOCTYPE html>
<html>

<head>
    <title>Laporan Stok Outlet</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left
        }

        th {
            background-color: #f2f2f2
        }

        .text-right {
            text-align: right
        }

        .header {
            background-color: #d9edf7;
            font-weight: bold
        }

        h2,
        h4 {
            text-align: center;
            margin: 5px 0
        }
    </style>
</head>

<body>
    <h2>Laporan Stok Bahan Baku per Outlet</h2>
    <h4>Outlet: {{ $namaOutlet }}</h4><br>
    @foreach ($stokGrouped as $namaOutletGrup => $stoks)
        <h3>{{ $namaOutletGrup }}</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bahan</th>
                    <th class="text-right">Jumlah Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stoks as $item)
                    <tr>
                        <td style="text-align:center">{{ $loop->iteration }}</td>
                        <td>{{ $item->nama_bahan }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                            {{ $item->satuan }}</td>
                </tr>@empty<tr>
                        <td colspan="3" style="text-align:center">Tidak ada data stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>

</html>
