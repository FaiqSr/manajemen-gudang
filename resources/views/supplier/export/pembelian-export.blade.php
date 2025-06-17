<!DOCTYPE html>
<html>

<head>
    <title>Riwayat Pembelian</title>
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

        h2,
        h4 {
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h2>Riwayat Pembelian Bahan Baku</h2>
    <h4>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4>
    <br>

    @foreach ($pembelians as $item)
        <table class="table table-sm table-bordered">
            <tr class="header">
                <td><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d F Y') }}
                </td>
                <td colspan="2"><strong>Supplier:</strong> {{ $item->nama_supplier }}</td>
                <td class="text-right"><strong>Total:</strong> Rp {{ number_format($item->total_biaya, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <th>Nama Bahan</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
            @if (isset($groupedDetails[$item->id]))
                @foreach ($groupedDetails[$item->id] as $detail)
                    <tr>
                        <td>{{ $detail->nama_bahan }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }}
                            {{ $detail->satuan }}</td>
                        <td class="text-right">{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
</body>

</html>
