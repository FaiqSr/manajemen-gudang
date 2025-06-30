@extends('layout.main')
@section('title', 'Laporan Piutang Usaha')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Piutang Usaha</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.piutang') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai"
                            class="form-control" value="{{ $tanggal_mulai }}"></div>
                    <div class="col-md-4 form-group"><label>Tanggal Selesai</label><input type="date"
                            name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"></div>
                    <div class="col-md-4 form-group"><button type="submit" class="btn btn-primary">Filter</button></div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Piutang Usaha ({{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }})</h3>
            <div class="card-tools">
                <a href="{{ route('laporan.piutang', array_merge(request()->query(), ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('laporan.piutang', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Tgl Penjualan</th>
                        <th class="text-right">Total Piutang</th>
                        <th class="text-right">Diterima</th>
                        <th class="text-right">Sisa Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataPiutang as $piutang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $piutang->nama_pelanggan }}</td>
                            <td>{{ \Carbon\Carbon::parse($piutang->tanggal_penjualan)->format('d M Y') }}</td>
                            <td class="text-right">{{ number_format($piutang->total_pendapatan, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($piutang->total_diterima, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">
                                {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="5" class="text-right">Total Sisa Piutang</td>
                        <td class="text-right">Rp {{ number_format($dataPiutang->sum('sisa_piutang'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
