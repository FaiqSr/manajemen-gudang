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
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
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

        h2,
        h3,
        h4 {
            text-align: center;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h2>Laporan Penyusutan Aktiva Tetap</h2>
    <h4>
        Outlet: {{ $namaOutlet ?: 'Semua Outlet (Total)' }} <br>
        Periode: {{ \Carbon\Carbon::create()->month($bulan_terpilih)->format('F') }} {{ $tahun_terpilih }}
    </h4>
    <br>

    @foreach ($asetGrouped as $namaOutletGrup => $assets)
        <h3>Aset: {{ $namaOutletGrup ?: 'Pusat' }}</h3>
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
                    <td>Total {{ $namaOutletGrup ?: 'Pusat' }}</td>
                    <td class="text-right">{{ $assets->sum('harga_perolehan') }}</td>
                    <td></td>
                    <td class="text-right">{{ $assets->sum('penyusutan_bulan_ini') }}</td>
                    <td class="text-right">{{ $assets->sum('akumulasi_penyusutan') }}</td>
                    <td class="text-right">{{ $assets->sum('nilai_buku') }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach
</body>

</html>
