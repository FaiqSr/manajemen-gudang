@extends('layout.main')
@section('title', 'Laporan Pembelian')

@section('breadcrums')
<div class="row mb-2">
    <div class="col-sm-6"><h1>Laporan Pembelian</h1></div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
        <div class="card-tools">
            @php
                $queryParams = ['id_supplier' => $supplier_id_terpilih, 'tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai];
            @endphp
            <a href="{{ route('laporan.pembelian', array_merge($queryParams, ['export' => 'excel'])) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
            <a href="{{ route('laporan.pembelian', array_merge($queryParams, ['export' => 'pdf'])) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('laporan.pembelian') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-4 form-group"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}"></div>
                <div class="col-md-4 form-group"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"></div>
                <div class="col-md-3 form-group">
                    <label>Supplier</label>
                    <select name="id_supplier" class="form-control">
                        <option value="">-- Semua Supplier --</option>
                        @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $supplier->id == $supplier_id_terpilih ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 form-group"><button type="submit" class="btn btn-primary btn-block">Filter</button></div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Riwayat Pembelian</h3></div>
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead><tr><th>No</th><th>Tanggal</th><th>No. Invoice</th><th>Supplier</th><th class="text-center">Jml. Item</th><th class="text-right">Total Biaya</th><th>Metode</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
            @forelse ($pembelians as $item)
            <tbody>
                <tr data-toggle="collapse" data-target="#detail-{{ $item->id }}" style="cursor: pointer;">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d F Y') }}</td>
                    <td>{{ $item->nomor_invoice }}</td>
                    <td>{{ $item->nama_supplier }}</td>
                    <td class="text-center">{{ $item->jumlah_item ?? 0 }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                    <td>{{ $item->metode_pembayaran }}</td>
                    <td><span class="badge {{ $item->status == 'Lunas' ? 'badge-success' : 'badge-warning' }}">{{ $item->status }}</span></td>
                    <td class="text-center"><button class="btn btn-xs btn-info"><i class="fas fa-eye"></i> Detail</button></td>
                </tr>
                <tr>
                    <td colspan="9" class="p-0" style="border-top: none;">
                        <div id="detail-{{ $item->id }}" class="collapse">
                            <div class="p-3">
                                <h6 class="text-bold">Rincian:</h6>
                                <table class="table table-sm table-striped">
                                    <thead><tr><th>Nama Bahan</th><th class="text-right">Jumlah</th><th class="text-right">Harga Satuan</th><th class="text-right">Subtotal</th></tr></thead>
                                    <tbody>
                                        @if(isset($groupedDetails[$item->id]))
                                            @foreach($groupedDetails[$item->id] as $detail)
                                            <tr>
                                                <td>{{ $detail->nama_bahan }}</td>
                                                <td class="text-right">{{ rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }} {{ $detail->satuan }}</td>
                                                <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            @empty
            <tbody><tr><td colspan="9" class="text-center">Tidak ada data pembelian pada periode ini.</td></tr></tbody>
            @endforelse
        </table>
    </div>
</div>
@endsection