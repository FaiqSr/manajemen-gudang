@extends('layout.main')

@section('title', 'Profil')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Profil</h1>
        </div>
        <div class="col-sm-6">

        </div>
    </div>
@endsection

@section('content')
    @php

        $data = DB::table('tbl_user')
            ->join('tbl_role', 'tbl_role.id', '=', 'tbl_user.id_role')
            ->select('tbl_user.*', 'tbl_role.nama_role')
            ->where('tbl_user.id', session()->get('id_user'))
            ->get();
    @endphp

    @foreach ($data as $item)
        @php
            $nama_lengkap = $item->nama_lengkap;
            $email = $item->email;
            $nama_role = $item->nama_role;
        @endphp
    @endforeach

    <div class="row">
        <div class="col-md-3">

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="rounded mx-auto d-block" src="{{ url('public/img/profil/kosong.jpg') }}" width="200"
                            height="200">
                    </div>
                    <h3 class="profile-username text-center">{{ $nama_lengkap }}</h3>
                    <p class="text-muted text-center">{{ $email }}</p>
                    <br><br>
                </div>

            </div>

        </div>

        <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <strong><i class="fas fa-user mr-1"></i> Nama Lengkap</strong>
                    <p class="text-muted ">{{ $nama_lengkap }}</p>
                    <hr>
                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                    <p class="text-muted ">{{ $email }}</p>
                    <hr>
                    <strong><i class="fas fa-group mr-1"></i> Level User</strong>
                    <p class="text-muted ">{{ $nama_role }}</p>
                    <hr>
                    <strong><i class="fas fa-book mr-1"></i> Jenis Kelamin</strong>
                    <p class="text-muted ">
                        @if ($item->id_gender == '1')
                            Laki - laki
                        @elseif ($item->id_gender == '2')
                            Perempuan
                        @endif
                        </h5>
                    </p>
                    <hr>

                </div>

            </div>
        </div>

    </div>


@endsection

@section('script')

    <script>
        function add_sukses() {
            Swal.fire({
                icon: 'success',
                title: ' &nbsp; Tambah Data Berhasil'
            });
        }
    </script>

    @if (session('add_sukses'))
        <script>
            add_sukses();
        </script>
    @endif

@endsection
