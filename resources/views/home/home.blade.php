@extends('layout.main')

@section('title', 'Home')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-12">
            <h1>Hai, {{ ucfirst(DB::table('tbl_user')->find(session()->get('id_user'))->nama_lengkap) }}</h1>
        </div>
    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Welcome</h5>
                </div>
                
                <div class="card-body">
                    <div class="text-center">
                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;"
                            src="https://startbootstrap.github.io/startbootstrap-sb-admin-2/img/undraw_posting_photo.svg"
                            alt="...">
                    </div>
                </div>                

            </div>

        </div>
    </div>

@endsection
