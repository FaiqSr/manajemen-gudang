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
            <form action="{{ url('outlet/operasional/add') }}" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <div class="ml-auto">
                                    <a href="{{ url('outlet/operasional/' . $id) }}" class="btn btn-default">
                                        <i class="fas fa fa-reply"></i> Kembali </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @csrf
                        <input type="hidden" name="idOulet" value="{{ $id }}">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Tanggal</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" name="tanggal" id="tanggal" autocomplete="off"
                                    required>
                            </div>
                        </div>
                        <div class="form-gorup row mb-3">
                            <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                            <div class="col-sm-4">
                                <textarea name="keterangan" id="keterangan" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jumlah</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="jumlah" id="jumlah" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Debit</label>
                            <div class="col-sm-4">
                                <select name="debit" id="debit" class="form-control">
                                    <option value="">--Pilih--</option>
                                    @foreach ($akuns as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_akun }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kredit</label>
                            <div class="col-sm-4">
                                <select name="kredit" id="kredit" class="form-control">
                                    <option value="">--Pilih--</option>
                                    @foreach ($akuns as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_akun }}</option>
                                    @endforeach
                                </select>
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
