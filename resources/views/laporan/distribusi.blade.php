@extends('layout.main')
@section('title', 'Laporan Distribusi Bahan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Distribusi Bahan</h1>
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
                <a href="{{ route('laporan.distribusi', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('laporan.distribusi', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.distribusi') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai"
                            class="form-control" value="{{ $tanggal_mulai }}"></div>
                    <div class="col-md-4 form-group"><label>Tanggal Selesai</label><input type="date"
                            name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"></div>
                    <div class="col-md-3 form-group">
                        <label>Outlet Tujuan</label>
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
            <h3 class="card-title">Riwayat Distribusi</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Outlet Tujuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                @forelse ($distribusis as $item)
                    <tbody>
                        <tr data-toggle="collapse" data-target="#detail-{{ $item->id }}" style="cursor: pointer;">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_distribusi)->format('d F Y') }}</td>
                            <td>{{ $item->nama_outlet }}</td>
                            <td class="text-center"><button class="btn btn-xs btn-info"><i class="fas fa-eye"></i> Detail
                                    Item</button></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="p-0" style="border-top: none;">
                                <div id="detail-{{ $item->id }}" class="collapse">
                                    <div class="p-3">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nama Bahan</th>
                                                    <th class="text-right">Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($groupedDetails[$item->id]))
                                                    @foreach ($groupedDetails[$item->id] as $detail)
                                                        <tr>
                                                            <td>{{ $detail->nama_bahan }}</td>
                                                            <td class="text-right">{{ $detail->jumlah }}
                                                                {{ $detail->satuan }}</td>
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
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data distribusi pada periode ini.</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
    </div>
@endsection
