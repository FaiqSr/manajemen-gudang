@extends('layout.main')
@section('title', 'Laporan Penyusutan Aset')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Penyusutan Aktiva Tetap</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
            <div class="card-tools">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.penyusutan') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Bulan</label>
                            <select name="bulan" class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == $bulan_terpilih ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ $tahun_terpilih }}"
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

    @foreach ($asetGrouped as $namaOutlet => $assets)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>Aset: {{ $namaOutlet ?: 'Pusat' }}</strong></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nama Aset</th>
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-right">Penyusutan / Bulan</th>
                            <th class="text-right">Penyusutan Bulan Ini</th>
                            <th class="text-right">Akumulasi Penyusutan</th>
                            <th class="text-right">Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $aset)
                            <tr>
                                <td>{{ $aset->nama_aset }}</td>
                                <td class="text-right">Rp {{ number_format($aset->harga_perolehan, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($aset->penyusutan_per_bulan, 0, ',', '.') }}
                                </td>
                                <td class="text-right">Rp {{ number_format($aset->penyusutan_bulan_ini, 0, ',', '.') }}
                                </td>
                                <td class="text-right">Rp {{ number_format($aset->akumulasi_penyusutan, 0, ',', '.') }}
                                </td>
                                <td class="text-right">Rp {{ number_format($aset->nilai_buku, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th>Total {{ $namaOutlet ?: 'Pusat' }}</th>
                            <th class="text-right">Rp {{ number_format($assets->sum('harga_perolehan'), 0, ',', '.') }}
                            </th>
                            <th></th>
                            <th class="text-right">Rp
                                {{ number_format($assets->sum('penyusutan_bulan_ini'), 0, ',', '.') }}</th>
                            <th class="text-right">Rp
                                {{ number_format($assets->sum('akumulasi_penyusutan'), 0, ',', '.') }}</th>
                            <th class="text-right">Rp {{ number_format($assets->sum('nilai_buku'), 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach
@endsection
