<!DOCTYPE html>
<html>

<head>
    <title>Laporan Buku Besar</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        h3,
        h4 {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h3>Buku Besar Akun: {{ $akunTerpilih->nama_akun }}</h3>
    <h4>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4>

    <table>
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th>Keterangan</th>
                <th class="text-right" width="15%">Debit</th>
                <th class="text-right" width="15%">Kredit</th>
                <th class="text-right" width="18%">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="4"><strong>Saldo Awal</strong></td>
                <td class="text-right"><strong>{{ $saldoAwal }}</strong></td>
            </tr>
            @php
                $saldoBerjalan = $saldoAwal;
            @endphp
            @foreach ($transaksi as $trx)
                @php
                    $perubahan =
                        $akunTerpilih->saldo_normal == 'Debit'
                            ? $trx->debit - $trx->kredit
                            : $trx->kredit - $trx->debit;
                    $saldoBerjalan += $perubahan;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d-m-Y') }}</td>
                    <td>{{ $trx->keterangan }} @if ($trx->nama_outlet)
                            ({{ $trx->nama_outlet }})
                        @endif
                    </td>
                    <td class="text-right">{{ $trx->debit }}</td>
                    <td class="text-right">{{ $trx->kredit }}</td>
                    <td class="text-right">{{ $saldoBerjalan }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="4">SALDO AKHIR</td>
                <td class="text-right">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
