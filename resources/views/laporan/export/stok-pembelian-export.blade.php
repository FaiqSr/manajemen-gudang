<!DOCTYPE html>
<html>

<head>
    <title>Laporan Stok & Pembelian Bahan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .header {
            background-color: #d9edf7;
            font-weight: bold;
        }

        h3 {
            margin-bottom: 0;
        }

        p {
            margin-top: 0;
        }
    </style>
</head>

<body>
    @foreach ($daftarBahan as $bahan)
        <div class="header">
            <h3>{{ $bahan->nama_bahan }}</h3>
            <p>Stok Gudang Saat Ini: {{ $bahan->jumlah_stok }} {{ $bahan->satuan }}</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Tanggal Pembelian</th>
                    <th>Supplier</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($detailPembelian[$bahan->id]))
                    @foreach ($detailPembelian[$bahan->id] as $pembelian)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d-m-Y') }}</td>
                            <td>{{ $pembelian->nama_supplier }}</td>
                            <td class="text-right">
                                {{ rtrim(rtrim(number_format($pembelian->jumlah, 2, ',', '.'), '0'), ',') }}
                                {{ $bahan->satuan }}</td>
                            <td class="text-right">{{ number_format($pembelian->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="text-align: center;"><em>Tidak ada riwayat pembelian pada periode yang
                                dipilih.</em></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</body>

</html>
