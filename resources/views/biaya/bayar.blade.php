@extends('layout.main')
@section('title', 'Bayar Biaya Operasional')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Pembayaran Biaya Operasional</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Pembayaran</h3>
        </div>
        <form action="{{ route('biaya.bayar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_biaya" value="{{ $biaya->id }}">
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Keterangan</dt>
                    <dd class="col-sm-9">{{ $biaya->keterangan }}</dd>
                    <dt class="col-sm-3">Outlet</dt>
                    <dd class="col-sm-9">{{ $biaya->nama_outlet }}</dd>
                    <dt class="col-sm-3">Total Tagihan</dt>
                    <dd class="col-sm-9"><strong>Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}</strong></dd>
                </dl>
                <hr>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Bayar Dari Akun</label>
                        <select name="id_akun_pembayaran" class="form-control" required>
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}">{{ $akun->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('biaya.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
@endsection
