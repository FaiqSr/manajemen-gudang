<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan</title>
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

        h2,
        h4 {
            text-align: center;
            margin: 2px 0
        }

        .text-right {
            text-align: right
        }

        .sub-table {
            width: 95%;
            margin-left: 5%;
            border: none
        }

        .sub-table td {
            border: none
        }
    </style>
</head>

<body>
    <h2>Laporan Penjualan</h2>
    <h4>Outlet: {{ $namaOutlet }} | Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Outlet</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualans as $penjualan)
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('d-m-Y') }}</td>
                    <td>{{ $penjualan->nama_outlet }}</td>
                    <td>{{ $penjualan->nama_pelanggan ?: '-' }}</td>
                    <td>{{ $penjualan->metode_pembayaran }}</td>
                    <td>{{ $penjualan->status }}</td>
                    <td class="text-right">{{ number_format($penjualan->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                @if (isset($groupedDetails[$penjualan->id]))
                    <tr>
                        <td colspan="7">
                            <table class="sub-table">
                                <tr>
                                    <th width="50%">Nama Bahan</th>
                                    <th class="text-right">Jumlah</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                                @foreach ($groupedDetails[$penjualan->id] as $detail)
                                    <tr>
                                        <td>{{ $detail->nama_bahan }}</td>
                                        <td class="text-right">{{ $detail->jumlah }}</td>
                                        <td class="text-right">
                                            {{ number_format($detail->harga_saat_transaksi, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>

</html>
