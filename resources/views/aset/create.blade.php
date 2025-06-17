@extends('layout.main')
@section('title', 'Tambah Aset Baru')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Tambah Aset Baru</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Aset</h3>
        </div>
        <form action="{{ route('aset.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nama Aset</label>
                        <input type="text" name="nama_aset" class="form-control" value="{{ old('nama_aset') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control"
                            value="{{ old('tanggal_perolehan', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Jenis Aset (Contoh: Elektronik, Mebel, dll)</label>
                        <input type="text" name="jenis_aset" class="form-control" value="{{ old('jenis_aset') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Penempatan Outlet (Opsional)</label>
                        <select name="id_outlet" class="form-control">
                            <option value="">-- Aset Pusat --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('id_outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <h5 class="text-bold">Informasi Keuangan</h5>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Harga Perolehan (Rp)</label>
                        <input type="number" name="harga_perolehan" class="form-control"
                            value="{{ old('harga_perolehan') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Masa Manfaat (Bulan)</label>
                        <input type="number" name="masa_manfaat_bulan" class="form-control"
                            value="{{ old('masa_manfaat_bulan') }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nilai Residu / Sisa (Rp)</label>
                        <input type="number" name="nilai_residu" class="form-control"
                            value="{{ old('nilai_residu') ?? 0 }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Akun Aset (Debit)</label>
                        <select name="id_akun_aset" class="form-control" required>
                            <option value="">-- Pilih Akun Aset --</option>
                            @foreach ($akunAset as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_aset') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sumber Dana / Pembayaran (Kredit)</label>
                        <select name="id_akun_pembayaran" class="form-control" required>
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_pembayaran') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
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
