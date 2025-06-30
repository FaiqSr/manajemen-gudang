@extends('layout.main')
@section('title', 'Penerimaan Piutang')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Penerimaan Pembayaran Piutang</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Penerimaan</h3>
        </div>
        <form action="{{ route('piutang.terima.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_penjualan" value="{{ $penjualan->id }}">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Pelanggan</dt>
                    <dd class="col-sm-8">{{ $penjualan->nama_pelanggan }}</dd>
                    <dt class="col-sm-4">Total Penjualan</dt>
                    <dd class="col-sm-8">Rp {{ number_format($penjualan->total_pendapatan, 0, ',', '.') }}</dd>
                    <dt class="col-sm-4">Sisa Tagihan</dt>
                    <dd class="col-sm-8"><strong>Rp {{ number_format($penjualan->sisa_piutang, 0, ',', '.') }}</strong></dd>
                </dl>
                <hr>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Tanggal Bayar</label><input type="date" name="tanggal_bayar"
                            class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="col-md-4 form-group">
                        <label>Jumlah Pembayaran</label>
                        <input type="number" name="jumlah_bayar" class="form-control"
                            value="{{ old('jumlah_bayar', $penjualan->sisa_piutang) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Masuk ke Akun</label>
                        <select name="id_akun_penerimaan" class="form-control" required>
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}">{{ $akun->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('piutang.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    @if (session('error'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
            Toast.fire({
                icon: 'error',
                title: ' &nbsp; {{ session('error') }}'
            });
        </script>
    @endif
@endsection
