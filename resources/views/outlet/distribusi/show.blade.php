@extends('layout.main')

@section('title', 'Riwayat Distribusi Outlet')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Riwayat Distribusi Bahan</h1>
            <h5 class="text-muted">Outlet: {{ $outlet->nama_outlet }}</h5>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('outlet/distribusi/' . $outlet->id) }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @forelse($distribusiGrouped as $bulanTahun => $items)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>{{ $bulanTahun }}</strong></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th width="20px">NO</th>
                            <th>Tanggal</th>
                            <th>Nama Bahan</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_distribusi)->format('d-m-Y') }}</td>
                                <td>{{ $item->nama_bahan }}</td>
                                <td class="text-right">{{ $item->jumlah }} {{ $item->satuan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body">
                <p class="text-center">Tidak ada data distribusi untuk periode yang dipilih.</p>
            </div>
        </div>
    @endforelse

@endsection
