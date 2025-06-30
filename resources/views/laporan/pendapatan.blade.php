@extends('layout.main')
@section('title', 'Laporan Pendapatan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Pendapatan</h1>
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
                        'id_outlet' => $outlet_id_terpilih,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                    ];
                @endphp
                <a href="{{ route('laporan.pendapatan', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('laporan.pendapatan', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.pendapatan') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai"
                            class="form-control" value="{{ $tanggal_mulai }}"></div>
                    <div class="col-md-4 form-group"><label>Tanggal Selesai</label><input type="date"
                            name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"></div>
                    <div class="col-md-3 form-group">
                        <label>Outlet</label>
                        <select name="id_outlet" class="form-control">
                            <option value="">-- Semua Outlet --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ $outlet->id == $outlet_id_terpilih ? 'selected' : '' }}>{{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 form-group"><button type="submit"
                            class="btn btn-primary btn-block">Filter</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rincian Pendapatan</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Sumber Pendapatan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanPendapatan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_akun }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data pendapatan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="2" class="text-right">Total Pendapatan</td>
                        <td class="text-right">Rp
                            {{ number_format($laporanPendapatan->sum('total_pendapatan'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
