@extends('layout.main')
@section('title', 'Ringkasan Pendapatan vs Biaya')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Ringkasan Pendapatan vs Biaya Operasional</h1>
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
                <a href="{{ route('laporan.ringkasan', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('laporan.ringkasan', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.ringkasan') }}" method="GET">
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

    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    <p>Total Pendapatan</p>
                </div>
                <div class="icon">
                    <i class="ion ion-arrow-up-c"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</h3>
                    <p>Total Biaya Operasional</p>
                </div>
                <div class="icon">
                    <i class="ion ion-arrow-down-c"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPendapatan - $totalBiayaOperasional, 0, ',', '.') }}</h3>
                    <p>Sisa (Pendapatan - Biaya)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-calculator"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rincian Biaya Operasional</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Biaya Operasional</th>
                        <th class="text-right" width="200px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rincianBiaya as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_akun }}</td>
                            <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data biaya operasional untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total Biaya Operasional</th>
                        <th class="text-right">Rp {{ number_format($totalBiayaOperasional, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
