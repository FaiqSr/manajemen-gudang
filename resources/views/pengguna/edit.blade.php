@extends('layout.main')

@section('title', 'User')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>User</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="ml-auto">
                                <h5>Edit Data</h5>
                            </div>
                        </div>
                        <div class="col mr-auto">
                            <div class="mr-auto float-right">
                                <a href="{{ url('pengguna/index') }}" class="btn btn-default">
                                    << Go Back to List </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <form action="{{ url('pengguna/edit') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap"
                                        value="{{ $row->nama_lengkap }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ $row->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" name="password" id="password"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="id_gender" id="id_gender" class="form-control">
                                        <option value="1"
                                            {{ old('id_gender', $row->id_gender) == 1 ? 'selected' : '' }}>
                                            Pria</option>
                                        <option value="2"
                                            {{ old('id_gender', $row->id_gender) == 2 ? 'selected' : '' }}>
                                            Wanita
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>User Grup</label>
                                    <select name="id_role" id="id_role" class="form-control">
                                        @foreach ($role as $dt)
                                            <option value="{{ $dt->id }}"
                                                {{ $row->id_role == $dt->id ? 'selected' : null }}>{{ $dt->nama_role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>


@endsection

@section('script')

    <script></script>

@endsection
