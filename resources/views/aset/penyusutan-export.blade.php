<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penyusutan Aktiva Tetap</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .group-header {
            background-color: #d9edf7;
            font-weight: bold;
        }

        .total-footer {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @foreach ($asetGrouped as $namaOutlet => $assets)
        <h3>Aset: {{ $namaOutlet ?: 'Pusat' }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Aset</th>
                    <th class="text-right">Harga Perolehan</th>
                    <th class="text-right">Penyusutan / Bulan</th>
                    <th class="text-right">Penyusutan Bulan Ini</th>
                    <th class="text-right">Akumulasi Penyusutan</th>
                    <th class="text-right">Nilai Buku</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $aset)
                    <tr>
                        <td>{{ $aset->nama_aset }}</td>
                        <td class="text-right">{{ $aset->harga_perolehan }}</td>
                        <td class="text-right">{{ $aset->penyusutan_per_bulan }}</td>
                        <td class="text-right">{{ $aset->penyusutan_bulan_ini }}</td>
                        <td class="text-right">{{ $aset->akumulasi_penyusutan }}</td>
                        <td class="text-right">{{ $aset->nilai_buku }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-footer">
                    <td>Total {{ $namaOutlet ?: 'Pusat' }}</td>
                    <td class="text-right">{{ $assets->sum('harga_perolehan') }}</td>
                    <td></td>
                    <td class="text-right">{{ $assets->sum('penyusutan_bulan_ini') }}</td>
                    <td class="text-right">{{ $assets->sum('akumulasi_penyusutan') }}</td>
                    <td class="text-right">{{ $assets->sum('nilai_buku') }}</td>
                </tr>
            </tfoot>
        </table>
        <br>
    @endforeach
</body>

</html>
