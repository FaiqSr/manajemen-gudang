@extends('layout.main')
@section('title', 'Laporan Stok Outlet')

@section('breadcrums')
<div class="row mb-2">
    <div class="col-sm-6"><h1>Laporan Stok Bahan Baku di Outlet</h1></div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
        <div class="card-tools">
            <a href="{{ route('laporan.stok-outlet', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
            <a href="{{ route('laporan.stok-outlet', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('laporan.stok-outlet') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-4 form-group">
                    <label>Pilih Outlet</label>
                    <select name="id_outlet" class="form-control">
                        <option value="">-- Semua Outlet --</option>
                        @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ $outlet->id == $outlet_id_terpilih ? 'selected' : '' }}>{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 form-group">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@forelse($stokGrouped as $namaOutlet => $stoks)
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><strong>Stok: {{ $namaOutlet }}</strong></h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm">
            <thead>
                <tr>
                    <th width="20px">NO</th>
                    <th>Nama Bahan Baku</th>
                    <th class="text-right" width="20%">Jumlah Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stoks as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_bahan }}</td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }} {{ $item->satuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center"><p>Tidak ada data stok untuk outlet yang dipilih.</p></div>
</div>
@endforelse
@endsection