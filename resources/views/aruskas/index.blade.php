@extends('layout.main')
@section('title', 'Transfer Kas ke Pusat')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Transfer Kas Antar Akun</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Transfer Kas</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('transfer-kas.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Transfer</label>
                            <input type="date" name="tanggal_transfer"
                                class="form-control @error('tanggal_transfer') is-invalid @enderror"
                                value="{{ old('tanggal_transfer', date('Y-m-d')) }}" required>
                            @error('tanggal_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                                placeholder="Masukkan jumlah" value="{{ old('jumlah') }}" required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Transfer Dari (Akun Sumber)</label>
                            <select name="akun_sumber" class="form-control @error('akun_sumber') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Akun Sumber --</option>
                                @foreach ($akunKasBank as $akun)
                                    <option value="{{ $akun->id }}"
                                        {{ old('akun_sumber') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_sumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Dari Outlet Mana?</label>
                            <select name="outlet_id" class="form-control @error('outlet_id') is-invalid @enderror">
                                <option value="">-- Pilih Outlet (jika sumbernya kas outlet) --</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('outlet_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Transfer Ke (Akun Tujuan)</label>
                            <select name="akun_tujuan" class="form-control @error('akun_tujuan') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Akun Tujuan --</option>
                                @foreach ($akunKasBank as $akun)
                                    <option value="{{ $akun->id }}"
                                        {{ old('akun_tujuan') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                            @error('akun_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="1">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    {{-- Menggunakan script notifikasi dari template asli Anda --}}
    <script>
        function add_sukses() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('add_sukses') }}'
            });
        }

        console.log('{{ session('error') }}');
    </script>

    @if (session('add_sukses'))
        <script>
            add_sukses();
        </script>
    @endif
@endsection
