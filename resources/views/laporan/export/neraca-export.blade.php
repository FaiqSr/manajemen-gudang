<!DOCTYPE html>
<html>

<head>
    <title>Laporan Neraca</title>
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
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        h2,
        h3 {
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h2>Laporan Neraca (Posisi Keuangan)</h2>
    <h3>Per Tanggal: {{ \Carbon\Carbon::parse($per_tanggal)->format('d F Y') }}</h3>
    <br>

    <table>
        <tr class="header">
            <th colspan="2">ASET</th>
        </tr>
        @foreach ($laporan['Aset'] as $nama_akun => $saldo)
            <tr>
                <td style="padding-left: 20px;">{{ $nama_akun }}</td>
                <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>TOTAL ASET</td>
            <td class="text-right">{{ number_format($laporan['totals']['aset'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <tr class="header">
            <th colspan="2">LIABILITAS DAN EKUITAS</th>
        </tr>
        <tr class="header">
            <th colspan="2" style="padding-left: 20px;">Liabilitas</th>
        </tr>
        @foreach ($laporan['Liabilitas'] as $nama_akun => $saldo)
            <tr>
                <td style="padding-left: 40px;">{{ $nama_akun }}</td>
                <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td style="padding-left: 20px;">Total Liabilitas</td>
            <td class="text-right">{{ number_format($laporan['totals']['liabilitas'], 0, ',', '.') }}</td>
        </tr>

        <tr class="header">
            <th colspan="2" style="padding-left: 20px;">Ekuitas</th>
        </tr>
        @foreach ($laporan['Ekuitas'] as $nama_akun => $saldo)
            <tr>
                <td style="padding-left: 40px;">{{ $nama_akun }}</td>
                <td class="text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td style="padding-left: 20px;">Total Ekuitas</td>
            <td class="text-right">{{ number_format($laporan['totals']['ekuitas'], 0, ',', '.') }}</td>
        </tr>

        <tr class="total">
            <td>TOTAL LIABILITAS DAN EKUITAS</td>
            <td class="text-right">
                {{ number_format($laporan['totals']['liabilitas'] + $laporan['totals']['ekuitas'], 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>
