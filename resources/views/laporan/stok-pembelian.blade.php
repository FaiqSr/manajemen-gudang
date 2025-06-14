@extends('layout.main')
@section('title', 'Laporan Stok & Pembelian')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Stok & Pembelian Bahan</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
            <div class="card-tools">
                <a href="{{ route('laporan.stok-pembelian', ['tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai, 'export' => 'excel']) }}"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('laporan.stok-pembelian', ['tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai, 'export' => 'pdf']) }}"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('laporan.stok-pembelian') }}" method="GET">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-5">
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

    @foreach ($daftarBahan as $bahan)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <strong>{{ $bahan->nama_bahan }}</strong>
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Stok Gudang Saat Ini:
                        {{ rtrim(rtrim(number_format($bahan->jumlah_stok, 2, ',', '.'), '0'), ',') }}
                        {{ $bahan->satuan }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="20%">Tanggal Pembelian</th>
                            <th>Supplier</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($detailPembelian[$bahan->id]))
                            @foreach ($detailPembelian[$bahan->id] as $pembelian)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d-m-Y') }}</td>
                                    <td>{{ $pembelian->nama_supplier }}</td>
                                    <td class="text-right">
                                        {{ rtrim(rtrim(number_format($pembelian->jumlah, 2, ',', '.'), '0'), ',') }}
                                        {{ $bahan->satuan }}</td>
                                    <td class="text-right">Rp {{ number_format($pembelian->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center"><em>Tidak ada riwayat pembelian pada periode yang
                                        dipilih.</em></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@endsection

@section('script')
@endsection
