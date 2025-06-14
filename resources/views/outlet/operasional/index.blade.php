@extends('layout.main')
@section('title', 'Biaya Operasional Outlet')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Catat Biaya Operasional Outlet</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Biaya Operasional</h3>
        </div>
        <form action="{{ route('outlet/operasional/store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Outlet</label>
                        <select name="id_outlet" class="form-control @error('id_outlet') is-invalid @enderror" required>
                            <option value="">-- Pilih Outlet --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('id_outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_outlet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Tanggal Biaya</label>
                        <input type="date" name="tanggal_biaya"
                            class="form-control @error('tanggal_biaya') is-invalid @enderror"
                            value="{{ old('tanggal_biaya', date('Y-m-d')) }}" required>
                        @error('tanggal_biaya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Jenis Biaya (Akun Beban)</label>
                        <select name="id_akun_beban" class="form-control @error('id_akun_beban') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            @foreach ($akunBeban as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_beban') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_akun_beban')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Sumber Pembayaran</label>
                        <select name="id_akun_pembayaran"
                            class="form-control @error('id_akun_pembayaran') is-invalid @enderror" required>
                            <option value="">-- Pilih Akun Pembayaran --</option>
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

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                            placeholder="Masukkan jumlah biaya" value="{{ old('jumlah') }}" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan"
                            class="form-control @error('keterangan') is-invalid @enderror"
                            placeholder="Contoh: Pembayaran Listrik Juni 2025" value="{{ old('keterangan') }}" required>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Biaya</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    @if (session('add_sukses'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('add_sukses') }}'
            });
        </script>
    @endif
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
