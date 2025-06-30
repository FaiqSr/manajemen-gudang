@extends('layout.main')
@section('title', 'Catat Biaya Operasional')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Catat Biaya Operasional</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Biaya</h3>
            <div class="card-tools"><a href="{{ route('biaya.index') }}" class="btn btn-info btn-sm"><i
                        class="fas fa-clipboard-list"></i> Daftar Hutang Biaya</a></div>
        </div>
        <form action="{{ route('biaya.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group"><label>Outlet</label><select name="id_outlet" class="form-control"
                            required>
                            <option value="">-- Pilih Outlet --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('id_outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group"><label>Tanggal Biaya/Tagihan</label><input type="date"
                            name="tanggal_biaya" class="form-control" value="{{ old('tanggal_biaya', date('Y-m-d')) }}"
                            required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group"><label>Jenis Biaya (Akun Beban)</label><select name="id_akun_beban"
                            class="form-control" required>
                            <option value="">-- Pilih Jenis Biaya --</option>
                            @foreach ($akunBeban as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_beban') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select></div>
                    <div class="col-md-6 form-group"><label>Jumlah (Rp)</label><input type="number" name="jumlah"
                            class="form-control" placeholder="Masukkan jumlah biaya" value="{{ old('jumlah') }}" required>
                    </div>
                </div>
                <div class="form-group"><label>Keterangan</label><input type="text" name="keterangan"
                        class="form-control" placeholder="Contoh: Tagihan Listrik Juni 2025"
                        value="{{ old('keterangan') }}" required></div>
                <hr>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Bayar
                                Langsung (Tunai)</option>
                            <option value="Digital/Bank"
                                {{ old('metode_pembayaran') == 'Digital/Bank' ? 'selected' : '' }}>Bayar Langsung
                                (Digital/Bank)</option>
                            <option value="Kredit" {{ old('metode_pembayaran') == 'Kredit' ? 'selected' : '' }}>Bayar Nanti
                                (Tempo/Kredit)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group" id="akun-pembayaran-div">
                        <label>Sumber Pembayaran</label>
                        <select name="id_akun_pembayaran" class="form-control">
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}"
                                    {{ old('id_akun_pembayaran') == $akun->id ? 'selected' : '' }}>{{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Transaksi</button></div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const metodePembayaran = document.getElementById('metode_pembayaran');
            const akunPembayaranDiv = document.getElementById('akun-pembayaran-div');

            function toggleAkunPembayaran() {
                akunPembayaranDiv.style.display = (metodePembayaran.value === 'Kredit') ? 'none' : 'block';
            }
            metodePembayaran.addEventListener('change', toggleAkunPembayaran);
            toggleAkunPembayaran();
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
