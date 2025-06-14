@extends('layout.main')

@section('title', 'supplier')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Tambah Data</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('supplier/addpembelian') }}" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <div class="ml-auto">
                                    <a href="{{ url('supplier/pembelian') }}" class="btn btn-default">
                                        <i class="fas fa fa-reply"></i> Kembali </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" name="tanggal" id="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Supplier</label>
                            <div class="col-sm-4">
                                <select name="idsupplier" id="idsupplier" class="form-control" required>
                                    <option value="">--Pilih Supplier--</option>
                                    @foreach ($supplier as $dt)
                                        <option value="{{ $dt->id }}">{{ $dt->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Item</label>
                            <div class="col-sm-4">
                                <select name="bahan" class="form-control" id="bahan">
                                    <option value="">--Pilih Item--</option>
                                    @foreach ($bahan as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jumlah</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="jumlah" id="jumlah" autocomplete="off"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Harga</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="harga" id="harga" autocomplete="off"
                                    required>
                            </div>
                        </div>
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>

            </form>

        </div>

    </div>

@endsection
