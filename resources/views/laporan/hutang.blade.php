@extends('layout.main')
@section('title', 'Laporan Hutang Usaha')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Hutang Usaha</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.hutang') }}" method="GET">
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
            <h3 class="card-title">Daftar Hutang Usaha ({{ \Carbon\Carbon::parse($tanggal_mulai)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($tanggal_selesai)->format('d M Y') }})</h3>
            <div class="card-tools">
                <a href="{{ route('laporan.hutang', array_merge(request()->query(), ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('laporan.hutang', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>No. Invoice</th>
                        <th>Tgl Pembelian</th>
                        <th class="text-right">Total Hutang</th>
                        <th class="text-right">Dibayar</th>
                        <th class="text-right">Sisa Hutang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataHutang as $hutang)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $hutang->nama_supplier }}</td>
                            <td>{{ $hutang->nomor_invoice }}</td>
                            <td>{{ \Carbon\Carbon::parse($hutang->tanggal_pembelian)->format('d M Y') }}</td>
                            <td class="text-right">{{ number_format($hutang->total_biaya, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($hutang->total_dibayar, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($hutang->sisa_hutang, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-light">
                        <td colspan="6" class="text-right">Total Sisa Hutang</td>
                        <td class="text-right">Rp {{ number_format($dataHutang->sum('sisa_hutang'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
