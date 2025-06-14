@extends('layout.main')

@section('title', 'Bahan Baku')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Bahan Baku</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('supplier/bahan/edit') }}" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <div class="ml-auto">
                                    <a href="{{ url('supplier/bahan/') }}" class="btn btn-default">
                                        <i class="fas fa fa-reply"></i> Kembali </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Bahan Baku</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="namabahan" id="namabahan"
                                    autocomplete="off" value="{{ $bahanBaku->nama_bahan }}" required>
                                <input type="hidden" name="id" value="{{ $bahanBaku->id }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Satuan</label>
                            <div class="col-sm-4">
                                <select name="satuan" id="satuan" class="form-control">
                                    <option value="">--Pilih Satuan--</option>
                                    <option value="kg" {{ $bahanBaku->satuan == 'kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="pcs" {{ $bahanBaku->satuan == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="liter" {{ $bahanBaku->satuan == 'liter' ? 'selected' : '' }}>Liter
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>

            </form>

        </div>

    </div>

@endsection
