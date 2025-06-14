@extends('layout.main')
@section('title', 'Tambah Aset Baru')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Tambah Aset</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Aset Baru</h3>
        </div>
        <form action="{{ route('aset.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nama Aset</label>
                        <input type="text" name="nama_aset" class="form-control @error('nama_aset') is-invalid @enderror"
                            value="{{ old('nama_aset') }}" required>
                        @error('nama_aset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Jenis Aset</label>
                        <input type="text" name="jenis_aset"
                            class="form-control @error('jenis_aset') is-invalid @enderror" value="{{ old('jenis_aset') }}">
                        @error('jenis_aset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Lokasi Aset (Outlet)</label>
                        <select name="id_outlet" class="form-control">
                            <option value="">Aset Pusat / Tidak ada Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('id_outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan"
                            class="form-control @error('tanggal_perolehan') is-invalid @enderror"
                            value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                        @error('tanggal_perolehan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Harga Perolehan (Rp)</label>
                        <input type="number" name="harga_perolehan"
                            class="form-control @error('harga_perolehan') is-invalid @enderror"
                            value="{{ old('harga_perolehan') }}" required>
                        @error('harga_perolehan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nilai Residu / Sisa (Rp)</label>
                        <input type="number" name="nilai_residu"
                            class="form-control @error('nilai_residu') is-invalid @enderror"
                            value="{{ old('nilai_residu', 0) }}" required>
                        @error('nilai_residu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Masa Manfaat (dalam Bulan)</label>
                        <input type="number" name="masa_manfaat_bulan"
                            class="form-control @error('masa_manfaat_bulan') is-invalid @enderror"
                            value="{{ old('masa_manfaat_bulan') }}" required>
                        @error('masa_manfaat_bulan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <hr>
                <h5>Pencatatan Akuntansi</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Kelompok Akun Aset (Debit)</label>
                        <select name="id_akun_aset" class="form-control @error('id_akun_aset') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Akun Aset --</option>
                            @foreach ($akunAset as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_aset') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_akun_aset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sumber Pembayaran (Kredit)</label>
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
            </div>
            <div class="card-footer">
                <a href="{{ route('aset.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Aset</button>
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
