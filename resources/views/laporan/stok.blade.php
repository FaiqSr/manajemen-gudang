@extends('layout.main')
@section('title', 'Laporan Stok Keseluruhan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Stok Keseluruhan</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
            <div class="card-tools">
                <a href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.stok') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group">
                        <label>Filter per Outlet</label>
                        <select name="id_outlet" class="form-control">
                            <option value="">-- Tampilkan Gudang & Semua Outlet --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ $outlet->id == $outlet_id_terpilih ? 'selected' : '' }}>{{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group"><button type="submit" class="btn btn-primary">Filter</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @if (!$outlet_id_terpilih)
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Stok Gudang Pusat</strong></h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th class="text-right">Jumlah Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokGudang as $item)
                                    <tr>
                                        <td>{{ $item->nama_bahan }}</td>
                                        <td class="text-right">
                                            {{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                                            {{ $item->satuan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">Stok gudang kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-{{ $outlet_id_terpilih ? '12' : '6' }}">
            @forelse($stokOutlet as $namaOutlet => $stoks)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Stok Outlet: {{ $namaOutlet }}</strong></h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Bahan</th>
                                    <th class="text-right">Jumlah Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stoks as $item)
                                    <tr>
                                        <td>{{ $item->nama_bahan }}</td>
                                        <td class="text-right">
                                            {{ rtrim(rtrim(number_format($item->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                                            {{ $item->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                @if ($outlet_id_terpilih)
                    <div class="card">
                        <div class="card-body text-center">
                            <p>Tidak ada data stok untuk outlet yang dipilih.</p>
                        </div>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
@endsection
