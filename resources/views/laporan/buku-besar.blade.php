@extends('layout.main')
@section('title', 'Buku Besar')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Buku Besar</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
            @if ($akun_terpilih_id)
                <div class="card-tools">
                    @php
                        $queryParams = [
                            'id_akun' => $akun_terpilih_id,
                            'tanggal_mulai' => $tanggal_mulai,
                            'tanggal_selesai' => $tanggal_selesai,
                        ];
                    @endphp
                    <a href="{{ route('laporan.buku-besar', array_merge($queryParams, ['export' => 'excel'])) }}"
                        class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('laporan.buku-besar', array_merge($queryParams, ['export' => 'pdf'])) }}"
                        class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.buku-besar') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Akun</label>
                            <select name="id_akun" class="form-control">
                                <option value="">-- Semua Akun --</option>
                                @foreach ($daftarAkun as $akun)
                                    <option value="{{ $akun->id }}"
                                        {{ $akun->id == $akun_terpilih_id ? 'selected' : '' }}>
                                        {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (!$akunUntukLaporan->isEmpty())
        @foreach ($akunUntukLaporan as $akun)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Buku Besar Akun: <strong>{{ $akun->nama_akun }}</strong>
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="12%">Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-right" width="15%">Debit</th>
                                <th class="text-right" width="15%">Kredit</th>
                                <th class="text-right" width="18%">Saldo Berjalan</th>
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
                                <td class="text-right"><strong>Rp {{ number_format($saldoAwal, 0, ',', '.') }}</strong></td>
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
                                        <td class="text-right">Rp {{ number_format($trx->debit, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($trx->kredit, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="4">SALDO AKHIR</th>
                                <th class="text-right">Rp {{ number_format($saldoBerjalan, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body">
                <p class="text-center">Silakan pilih akun untuk menampilkan buku besar.</p>
            </div>
        </div>
    @endif
@endsection
