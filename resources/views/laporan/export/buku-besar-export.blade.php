<!DOCTYPE html>
<html>

<head>
    <title>Laporan Buku Besar</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
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
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        h2,
        h3,
        h4 {
            margin: 5px 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <h2>Laporan Buku Besar</h2>
    <h4>Periode: {{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
        {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }}</h4>
    <hr>

    @foreach ($akunUntukLaporan as $akun)
        <h3>Akun: {{ $akun->nama_akun }}</h3>
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
                @php
                    $saldoAwal = $saldoAwalGrouped[$akun->id]->saldo ?? 0;
                    if ($akun->saldo_normal == 'Kredit') {
                        $saldoAwal *= -1;
                    }
                    $saldoBerjalan = $saldoAwal;
                @endphp
                <tr>
                    <td colspan="4"><strong>Saldo Awal</strong></td>
                    <td class="text-right"><strong>{{ $saldoAwal }}</strong></td>
                </tr>

                @if (isset($transaksiGrouped[$akun->id]))
                    @foreach ($transaksiGrouped[$akun->id] as $trx)
                        @php
                            $perubahan =
                                $akun->saldo_normal == 'Debit'
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
                @endif
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="4">SALDO AKHIR</td>
                    <td class="text-right">{{ $saldoBerjalan }}</td>
                </tr>
            </tfoot>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
