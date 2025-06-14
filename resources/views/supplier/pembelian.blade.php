@extends('layout.main')
@section('title', 'Pembelian Bahan Baku')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Pembelian Bahan Baku</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulir Pembelian</h3>
        </div>
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tanggal Pembelian</label>
                        <input type="date" name="tanggal_pembelian"
                            class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                            value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                        @error('tanggal_pembelian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control @error('id_supplier') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5>Detail Bahan yang Dibeli</h5>
                <div id="bahan-container">
                </div>
                <button type="button" class="btn btn-success btn-sm mb-3" id="tambah-bahan">Tambah Bahan</button>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th width="15%">Jumlah</th>
                            <th width="25%">Subtotal (Rp)</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bahan-rows">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total Biaya</th>
                            <th colspan="2" id="total-biaya" class="text-right">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
            </div>
        </form>
    </div>

    <table style="display: none;">
        <tbody id="bahan-template">
            <tr class="bahan-item">
                <td>
                    <select name="bahan[0][id]" class="form-control bahan-select" required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach ($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">{{ $bahan->nama_bahan }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="bahan[0][jumlah]" class="form-control jumlah-input" placeholder="Jumlah"
                            min="0.01" step="any" required>
                        <div class="input-group-append">
                            <span class="input-group-text satuan-text"></span>
                        </div>
                    </div>
                </td>
                <td>
                    <input type="number" name="bahan[0][subtotal]" class="form-control subtotal-input"
                        placeholder="Subtotal" min="0" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm hapus-bahan">Hapus</button>
                </td>
            </tr>
        </tbody>
    </table>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let bahanIndex = 0;
            const tableBody = document.getElementById('bahan-rows');
            const template = document.getElementById('bahan-template');

            document.getElementById('tambah-bahan').addEventListener('click', function() {
                const clone = template.querySelector('.bahan-item').cloneNode(true);

                clone.querySelector('.bahan-select').name = `bahan[${bahanIndex}][id]`;
                clone.querySelector('.jumlah-input').name = `bahan[${bahanIndex}][jumlah]`;
                clone.querySelector('.subtotal-input').name = `bahan[${bahanIndex}][subtotal]`;

                tableBody.appendChild(clone);
                bahanIndex++;
            });

            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('hapus-bahan')) {
                    e.target.closest('.bahan-item').remove();
                    updateTotal();
                }
            });

            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('bahan-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const satuan = selectedOption.getAttribute('data-satuan') || '';
                    e.target.closest('.bahan-item').querySelector('.satuan-text').textContent = satuan;
                }
            });

            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('subtotal-input')) {
                    updateTotal();
                }
            });

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.subtotal-input').forEach(function(input) {
                    total += parseFloat(input.value) || 0;
                });
                document.getElementById('total-biaya').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
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
