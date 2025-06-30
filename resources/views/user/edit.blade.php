@extends('layout.main')
@section('title', 'Edit User')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit User</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Edit User</h3>
        </div>
        <form action="{{ route('user.update', $user->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap"
                        class="form-control @error('nama_lengkap') is-invalid @enderror"
                        value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}"
                        required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group"><label>Password Baru (Opsional)</label><input type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Kosongkan jika tidak ingin diubah">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group"><label>Konfirmasi Password Baru</label><input type="password"
                        name="password_confirmation" class="form-control"></div>
                <div class="row">
                    <div class="col-md-4 form-group"><label>Role</label><select name="id_role" class="form-control"
                            required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('id_role', $user->id_role) == $role->id ? 'selected' : '' }}>
                                    {{ $role->nama_role }}</option>
                            @endforeach
                        </select></div>
                    <div class="col-md-4 form-group"><label>Gender</label><select name="id_gender" class="form-control"
                            required>
                            <option value="1" {{ old('id_gender', $user->id_gender) == 1 ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="2" {{ old('id_gender', $user->id_gender) == 2 ? 'selected' : '' }}>
                                Perempuan</option>
                        </select></div>
                    <div class="col-md-4 form-group"><label>Status</label><select name="is_active" class="form-control"
                            required>
                            <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="0" {{ old('is_active', $user->is_active) === 0 ? 'selected' : '' }}>Tidak
                                Aktif</option>
                        </select></div>
                </div>
            </div>
            <div class="card-footer"><a href="{{ route('user.index') }}" class="btn btn-secondary">Batal</a><button
                    type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div>
@endsection
