<!DOCTYPE html>
<html>

<head>
    <title>Laporan Stok Keseluruhan</title>
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
        h3 {
            margin: 5px 0
        }
    </style>
</head>

<body>
    <h2>Laporan Stok Keseluruhan</h2>
    <h4>Outlet: {{ $namaOutlet ?: 'Gudang & Semua Outlet' }}</h4>
    @if (!$namaOutlet)
        <h3>Stok Gudang Pusat</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Bahan</th>
                    <th class="text-right">Jumlah Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokGudang as $item)
                    <tr>
                        <td>{{ $item->nama_bahan }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                            {{ $item->satuan }}</td>
                </tr>@empty<tr>
                        <td colspan="2" style="text-align:center">Stok gudang kosong.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>@endif @foreach ($stokOutlet as $namaOutletGrup => $stoks)
            <h3>Stok Outlet: {{ $namaOutletGrup }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th class="text-right">Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stoks as $item)
                        <tr>
                            <td>{{ $item->nama_bahan }}</td>
                            <td class="text-right">
                                {{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                                {{ $item->satuan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
</body>

</html>
