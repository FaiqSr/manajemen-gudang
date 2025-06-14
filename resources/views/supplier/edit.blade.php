@extends('layout.main')

@section('title', 'supplier')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit supplier</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('supplier/edit') }}" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <div class="ml-auto">
                                    <a href="{{ url('supplier/index') }}" class="btn btn-default">
                                        <i class="fas fa fa-reply"></i> Kembali </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Supplier</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="namasupplier" id="namasupplier"
                                    autocomplete="off" value="{{ $row->namasupplier }}" required>
                                <input type="hidden" name="id" value="{{ $row->id }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Telpon</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="telpon" id="telpon" autocomplete="off"
                                    value="{{ $row->telpon }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-4">
                                <textarea name="alamat" id="alamat" cols="2" rows="3" class="form-control" required>{{ $row->alamat }}</textarea>
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
