@extends('layout.main')
@section('title', 'Laporan Laba Rugi')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Laba Rugi</h1>
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
                <a href="{{ route('laporan.laba-rugi', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('laporan.laba-rugi', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.laba-rugi') }}" method="GET">
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hasil Laporan</h3>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <tr class="bg-light">
                    <th colspan="2">PENDAPATAN</th>
                </tr>
                @forelse($laporan['Pendapatan'] as $nama_akun => $total)
                    <tr>
                        <td class="pl-4">{{ $nama_akun }}</td>
                        <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="pl-4"><em>Tidak ada pendapatan</em></td>
                    </tr>
                @endforelse
                <tr>
                    <th class="text-right">TOTAL PENDAPATAN</th>
                    <th class="text-right">Rp {{ number_format($laporan['totals']['pendapatan'], 0, ',', '.') }}</th>
                </tr>

                <tr class="bg-light">
                    <th colspan="2">BEBAN POKOK PENJUALAN</th>
                </tr>
                @forelse($laporan['Beban Pokok Penjualan'] as $nama_akun => $total)
                    <tr>
                        <td class="pl-4">{{ $nama_akun }}</td>
                        <td class="text-right">(Rp {{ number_format($total, 0, ',', '.') }})</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="pl-4"><em>Tidak ada HPP</em></td>
                    </tr>
                @endforelse
                <tr class="bg-light">
                    <th class="text-right">LABA KOTOR</th>
                    <th class="text-right">Rp {{ number_format($laporan['totals']['laba_kotor'], 0, ',', '.') }}</th>
                </tr>

                <tr class="bg-light">
                    <th colspan="2">BEBAN OPERASIONAL</th>
                </tr>
                @forelse($laporan['Beban Operasional'] as $nama_akun => $total)
                    <tr>
                        <td class="pl-4">{{ $nama_akun }}</td>
                        <td class="text-right">(Rp {{ number_format($total, 0, ',', '.') }})</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="pl-4"><em>Tidak ada beban operasional</em></td>
                    </tr>
                @endforelse
                <tr>
                    <th class="text-right">TOTAL BEBAN OPERASIONAL</th>
                    <th class="text-right">(Rp {{ number_format($laporan['totals']['operasional'], 0, ',', '.') }})</th>
                </tr>

                <tr class="bg-success">
                    <th class="text-right">LABA BERSIH</th>
                    <th class="text-right">Rp {{ number_format($laporan['totals']['laba_bersih'], 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    </div>
@endsection
