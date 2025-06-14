@extends('layout.main')
@section('title', 'Edit Produk')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Produk</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Edit Produk</h3>
        </div>
        <form action="{{ route('produk.update', $produk->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror"
                        value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                    @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Harga Produksi / HPP (Rp)</label>
                    <input type="number" name="harga_produksi"
                        class="form-control @error('harga_produksi') is-invalid @enderror"
                        value="{{ old('harga_produksi', $produk->harga_produksi) }}" required>
                    @error('harga_produksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror"
                        value="{{ old('harga_jual', $produk->harga_jual) }}" required>
                    @error('harga_jual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Produk</button>
            </div>
        </form>
    </div>
@endsection
