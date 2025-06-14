@extends('layout.main')
@section('title', 'Laporan Arus Kas')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Arus Kas</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
            <div class="card-tools">
                @php
                    $queryParams = [
                        'outlet_id' => $outlet_id_terpilih,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                    ];
                @endphp
                <a href="{{ route('laporan.arus-kas', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('laporan.arus-kas', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.arus-kas') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Outlet</label>
                            <select name="outlet_id" class="form-control">
                                <option value="">-- Semua Outlet (Total) --</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ $outlet->id == $outlet_id_terpilih ? 'selected' : '' }}>
                                        {{ $outlet->nama_outlet }}
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Arus Kas (Metode Langsung)</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <tr>
                    <th>Saldo Kas Awal Periode</th>
                    <th class="text-right">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</th>
                </tr>

                <tr class="bg-light">
                    <th colspan="2">Arus Kas dari Aktivitas Operasi</th>
                </tr>
                <tr>
                    <td class="pl-4">Penerimaan dari Pelanggan</td>
                    <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_operasi'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="pl-4">Pembayaran ke Supplier & Beban Operasional</td>
                    <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_operasi'], 0, ',', '.') }})</td>
                </tr>
                @php $net_operasi = $laporan['totals']['masuk_operasi'] - $laporan['totals']['keluar_operasi']; @endphp
                <tr>
                    <th class="pl-4">Arus Kas Bersih dari Aktivitas Operasi</th>
                    <th class="text-right">Rp {{ number_format($net_operasi, 0, ',', '.') }}</th>
                </tr>

                <tr class="bg-light">
                    <th colspan="2">Arus Kas dari Aktivitas Investasi</th>
                </tr>
                @php $net_investasi = $laporan['totals']['masuk_investasi'] - $laporan['totals']['keluar_investasi']; @endphp
                <tr>
                    <td class="pl-4">Pembelian Aset Tetap</td>
                    <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_investasi'], 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <td class="pl-4">Penjualan Aset Tetap</td>
                    <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_investasi'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th class="pl-4">Arus Kas Bersih dari Aktivitas Investasi</th>
                    <th class="text-right">Rp {{ number_format($net_investasi, 0, ',', '.') }}</th>
                </tr>

                <tr class="bg-light">
                    <th colspan="2">Arus Kas dari Aktivitas Pendanaan</th>
                </tr>
                @php $net_pendanaan = $laporan['totals']['masuk_pendanaan'] - $laporan['totals']['keluar_pendanaan']; @endphp
                <tr>
                    <td class="pl-4">Setoran Modal / Penerimaan Pinjaman</td>
                    <td class="text-right">Rp {{ number_format($laporan['totals']['masuk_pendanaan'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="pl-4">Pembayaran Utang Bank</td>
                    <td class="text-right">(Rp {{ number_format($laporan['totals']['keluar_pendanaan'], 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <th class="pl-4">Arus Kas Bersih dari Aktivitas Pendanaan</th>
                    <th class="text-right">Rp {{ number_format($net_pendanaan, 0, ',', '.') }}</th>
                </tr>

                @php $kenaikan_bersih = $net_operasi + $net_investasi + $net_pendanaan; @endphp
                <tr>
                    <th>Kenaikan (Penurunan) Bersih Kas</th>
                    <th class="text-right">Rp {{ number_format($kenaikan_bersih, 0, ',', '.') }}</th>
                </tr>
                @php $saldo_akhir = $saldoAwal + $kenaikan_bersih; @endphp
                <tr class="bg-success">
                    <th>SALDO KAS AKHIR PERIODE</th>
                    <th class="text-right">Rp {{ number_format($saldo_akhir, 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    </div>
@endsection
