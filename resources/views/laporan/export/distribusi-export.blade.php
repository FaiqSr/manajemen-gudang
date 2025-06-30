<!DOCTYPE html>
<html>

<head>
    <title>Laporan Distribusi Bahan</title>
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
    <h2>Laporan Distribusi Bahan</h2>
    <h4>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4><br>
    @foreach ($distribusis as $item)
        <table class="table table-sm table-bordered">
            <tr class="header">
                <td><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->tanggal_distribusi)->format('d F Y') }}
                </td>
                <td><strong>Outlet Tujuan:</strong> {{ $item->nama_outlet }}</td>
            </tr>
            <tr>
                <th>Nama Bahan</th>
                <th class="text-right">Jumlah</th>
            </tr>
            @if (isset($groupedDetails[$item->id]))
                @foreach ($groupedDetails[$item->id] as $detail)
                    <tr>
                        <td>{{ $detail->nama_bahan }}</td>
                        <td class="text-right">{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
    @endforeach
</body>

</html>
