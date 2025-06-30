@extends('layout.main')
@section('title', 'Edit Bahan Baku')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6"><h1>Edit Bahan Baku</h1></div>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Formulir Edit Bahan Baku</h3></div>
    <form action="{{ route('bahan.update', $bahanBaku->id) }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Bahan</label>
                <input type="text" name="nama_bahan" class="form-control @error('nama_bahan') is-invalid @enderror" value="{{ old('nama_bahan', $bahanBaku->nama_bahan) }}" required>
                @error('nama_bahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
             <div class="form-group">
                <label>Satuan</label>
                <select name="satuan" class="form-control" required>
                    @foreach($satuans as $satuan)
                    <option value="{{ $satuan->nama_satuan }}" {{ old('satuan', $bahanBaku->satuan) == $satuan->nama_satuan ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>
             <div class="form-group">
                <label>Harga Pokok (Rp)</label>
                <input type="number" name="harga_pokok" class="form-control @error('harga_pokok') is-invalid @enderror" value="{{ old('harga_pokok', $bahanBaku->harga_pokok ?? 0) }}" required>
                @error('harga_pokok')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ url('bahan') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Update Data</button>
        </div>
    </form>
</div>
@endsection