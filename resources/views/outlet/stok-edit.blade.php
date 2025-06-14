@extends('layout.main')

@section('title', 'Edit Stok Outlet - ' . $stokItem->nama_outlet)

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Stok: {{ $stokItem->nama_bahan }}</h1>
            <small>Outlet: {{ $stokItem->nama_outlet }}</small>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('outlet') }}">Outlet</a></li>
                <li class="breadcrumb-item"><a href="{{ route('outlet.stok', ['outlet_id' => $stokItem->id_outlet]) }}">Stok
                        {{ $stokItem->nama_outlet }}</a></li>
                <li class="breadcrumb-item active">Edit Stok</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('outlet.stok.update') }}" method="post">
                @csrf
                <input type="hidden" name="stok_id" value="{{ $stokItem->id }}">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Stok</h3>
                        <div class="card-tools">
                            <a href="{{ route('outlet.stok', ['outlet_id' => $stokItem->id_outlet]) }}"
                                class="btn btn-sm btn-default">
                                <i class="fas fa-reply"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="nama_bahan">Nama Bahan</label>
                            <input type="text" class="form-control" id="nama_bahan" value="{{ $stokItem->nama_bahan }}"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_stok">Jumlah Stok ({{ $stokItem->satuan }})</label>
                            <input type="number" step="any"
                                class="form-control @error('jumlah_stok') is-invalid @enderror" name="jumlah_stok"
                                id="jumlah_stok" value="{{ old('jumlah_stok', $stokItem->jumlah_stok) }}" required>
                            @error('jumlah_stok')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
