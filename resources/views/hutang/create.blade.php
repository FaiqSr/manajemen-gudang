@extends('layout.main')
@section('title', 'Pembayaran Hutang')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Pembayaran Hutang</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Pembayaran</h3>
        </div>
        <form action="{{ route('hutang.bayar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_pembelian" value="{{ $pembelian->id }}">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Supplier</dt>
                    <dd class="col-sm-8">{{ $pembelian->nama_supplier }}</dd>
                    <dt class="col-sm-4">No. Invoice</dt>
                    <dd class="col-sm-8">{{ $pembelian->nomor_invoice }}</dd>
                    <dt class="col-sm-4">Tanggal Pembelian</dt>
                    <dd class="col-sm-8">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d F Y') }}</dd>
                    <dt class="col-sm-4">Total Tagihan</dt>
                    <dd class="col-sm-8"><strong>Rp {{ number_format($pembelian->total_biaya, 0, ',', '.') }}</strong></dd>
                </dl>
                <hr>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar"
                            class="form-control @error('tanggal_bayar') is-invalid @enderror"
                            value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                        @error('tanggal_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Jumlah Pembayaran</label>
                        <input type="number" name="jumlah_bayar"
                            class="form-control @error('jumlah_bayar') is-invalid @enderror"
                            value="{{ old('jumlah_bayar', $pembelian->total_biaya) }}" required>
                        @error('jumlah_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Bayar Dari Akun</label>
                        <select name="id_akun_pembayaran"
                            class="form-control @error('id_akun_pembayaran') is-invalid @enderror" required>
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_pembayaran') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_akun_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('hutang.index') }}" class="btn btn-secondary">Batal</a>
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
