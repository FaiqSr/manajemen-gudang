@extends('layout.main')
@section('title', 'Distribusi Bahan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Distribusi Bahan ke Outlet</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Distribusi</h3>
        </div>
        <form action="{{ route('distribusi.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Distribusi</label>
                            <input type="date" name="tanggal_distribusi"
                                class="form-control @error('tanggal_distribusi') is-invalid @enderror"
                                value="{{ old('tanggal_distribusi', date('Y-m-d')) }}" required>
                            @error('tanggal_distribusi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Distribusi ke Outlet</label>
                            <select name="id_outlet_tujuan"
                                class="form-control @error('id_outlet_tujuan') is-invalid @enderror" required>
                                <option value="">-- Pilih Outlet Tujuan --</option>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}"
                                        {{ old('id_outlet_tujuan') == $outlet->id ? 'selected' : '' }}>
                                        {{ $outlet->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_outlet_tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <h5>Detail Bahan</h5>
                <div id="bahan-container">
                </div>
                <button type="button" class="btn btn-success btn-sm" id="tambah-bahan">Tambah Bahan</button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Distribusi</button>
            </div>
        </form>
    </div>

    <div id="bahan-template" style="display: none;">
        <div class="row bahan-item" style="margin-bottom: 10px;">
            <div class="col-md-6">
                <select name="bahan[0][id]" class="form-control bahan-select" required>
                    <option value="">-- Pilih Bahan Baku --</option>
                    @foreach ($bahanBaku as $bahan)
                        <option value="{{ $bahan->id }}" data-stok="{{ $bahan->stok_tersedia }}"
                            data-satuan="{{ $bahan->satuan }}">{{ $bahan->nama_bahan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="bahan[0][jumlah]" class="form-control" placeholder="Jumlah" min="1"
                    step="any" required>
                <small class="form-text text-muted stok-info"></small>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-block hapus-bahan">Hapus</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let bahanIndex = 0;
            const container = document.getElementById('bahan-container');
            const template = document.getElementById('bahan-template');

            document.getElementById('tambah-bahan').addEventListener('click', function() {
                const clone = template.firstElementChild.cloneNode(true);

                clone.querySelector('.bahan-select').name = `bahan[${bahanIndex}][id]`;
                clone.querySelector('input[type="number"]').name = `bahan[${bahanIndex}][jumlah]`;

                container.appendChild(clone);
                bahanIndex++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('hapus-bahan')) {
                    e.target.closest('.bahan-item').remove();
                }
            });

            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('bahan-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const stok = selectedOption.getAttribute('data-stok');
                    const satuan = selectedOption.getAttribute('data-satuan');
                    const stokInfo = e.target.closest('.bahan-item').querySelector('.stok-info');
                    if (stok) {
                        stokInfo.textContent = `Stok tersedia: ${stok} ${satuan}`;
                    } else {
                        stokInfo.textContent = '';
                    }
                }
            });
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
