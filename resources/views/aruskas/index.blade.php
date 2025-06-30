@extends('layout.main')
@section('title', 'Transfer Kas')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Transfer Kas Antar Akun</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Formulir Transfer Kas</h3>
        </div>
        <form action="{{ route('transfer-kas.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tanggal Transfer</label>
                        <input type="date" name="tanggal_transfer"
                            class="form-control @error('tanggal_transfer') is-invalid @enderror"
                            value="{{ old('tanggal_transfer', date('Y-m-d')) }}" required>
                        @error('tanggal_transfer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                            value="{{ old('jumlah') }}" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}"
                        placeholder="Contoh: Setoran kas dari Outlet Depok ke Bank">
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5><i class="fas fa-arrow-circle-up text-danger"></i> Sumber Dana</h5>
                            <div class="form-group">
                                <label>Akun Sumber</label>
                                <select name="akun_sumber" id="akun_sumber" class="form-control" required>
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach ($akunKasBank as $akun)
                                        <option value="{{ $akun->id }}"
                                            {{ old('akun_sumber') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="outlet-sumber-div" style="display:none;">
                                <label>Dari Outlet</label>
                                <select name="outlet_sumber_id" class="form-control">
                                    <option value="">-- Pilih Outlet --</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ old('outlet_sumber_id') == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5><i class="fas fa-arrow-circle-down text-success"></i> Akun Tujuan</h5>
                            <div class="form-group">
                                <label>Akun Tujuan</label>
                                <select name="akun_tujuan" id="akun_tujuan" class="form-control" required>
                                    <option value="">-- Pilih Akun --</option>
                                    @foreach ($akunKasBank as $akun)
                                        <option value="{{ $akun->id }}"
                                            {{ old('akun_tujuan') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="outlet-tujuan-div" style="display:none;">
                                <label>Ke Outlet</label>
                                <select name="outlet_tujuan_id" class="form-control">
                                    <option value="">-- Pilih Outlet --</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}"
                                            {{ old('outlet_tujuan_id') == $outlet->id ? 'selected' : '' }}>
                                            {{ $outlet->nama_outlet }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const akunSumberSelect = document.getElementById('akun_sumber');
            const outletSumberDiv = document.getElementById('outlet-sumber-div');
            const akunTujuanSelect = document.getElementById('akun_tujuan');
            const outletTujuanDiv = document.getElementById('outlet-tujuan-div');

            function toggleOutletSumber() {
                outletSumberDiv.style.display = (akunSumberSelect.value == '2') ? 'block' : 'none';
            }

            function toggleOutletTujuan() {
                outletTujuanDiv.style.display = (akunTujuanSelect.value == '2') ? 'block' : 'none';
            }

            akunSumberSelect.addEventListener('change', toggleOutletSumber);
            akunTujuanSelect.addEventListener('change', toggleOutletTujuan);

            toggleOutletSumber();
            toggleOutletTujuan();
        });
    </script>

    @if (session('add_sukses'))
        <script>
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
        </script>
    @endif
    @if (session('error'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
            Toast.fire({
                icon: 'error',
                title: ' &nbsp; {{ session('error') }}'
            });
        </script>
    @endif
@endsection
