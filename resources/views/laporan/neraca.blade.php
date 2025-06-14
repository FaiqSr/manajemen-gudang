@extends('layout.main')
@section('title', 'Laporan Neraca')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Neraca (Posisi Keuangan)</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.neraca') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Per Tanggal</label>
                            <input type="date" name="per_tanggal" class="form-control" value="{{ $per_tanggal }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                            <a href="{{ route('laporan.neraca', ['per_tanggal' => $per_tanggal, 'export' => 'excel']) }}"
                                class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                            <a href="{{ route('laporan.neraca', ['per_tanggal' => $per_tanggal, 'export' => 'pdf']) }}"
                                class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Neraca per tanggal {{ \Carbon\Carbon::parse($per_tanggal)->format('d F Y') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th colspan="2">ASET</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan['Aset'] as $nama_akun => $saldo)
                                <tr>
                                    <td class="pl-4">{{ $nama_akun }}</td>
                                    <td class="text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary">
                                <th>TOTAL ASET</th>
                                <th class="text-right">Rp {{ number_format($laporan['totals']['aset'], 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th colspan="2">LIABILITAS DAN EKUITAS</th>
                            </tr>
                            <tr class="bg-light">
                                <th colspan="2" class="pl-4">Liabilitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan['Liabilitas'] as $nama_akun => $saldo)
                                <tr>
                                    <td class="pl-4">{{ $nama_akun }}</td>
                                    <td class="text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th class="text-right pl-4">Total Liabilitas</th>
                                <th class="text-right">Rp
                                    {{ number_format($laporan['totals']['liabilitas'], 0, ',', '.') }}</th>
                            </tr>

                            <tr class="bg-light">
                                <th colspan="2" class="pl-4">Ekuitas</th>
                            </tr>
                            @foreach ($laporan['Ekuitas'] as $nama_akun => $saldo)
                                <tr>
                                    <td class="pl-4">{{ $nama_akun }}</td>
                                    <td class="text-right">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th class="text-right pl-4">Total Ekuitas</th>
                                <th class="text-right">Rp {{ number_format($laporan['totals']['ekuitas'], 0, ',', '.') }}
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary">
                                <th>TOTAL LIABILITAS DAN EKUITAS</th>
                                <th class="text-right">Rp
                                    {{ number_format($laporan['totals']['liabilitas'] + $laporan['totals']['ekuitas'], 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
