@extends('layout.main')
@section('title', 'Jurnal Umum')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Jurnal Umum</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Buat Entri Jurnal Baru</h3>
        </div>
        <form action="{{ route('jurnal.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tanggal Transaksi</label>
                        <input type="date" name="tanggal_transaksi"
                            class="form-control @error('tanggal_transaksi') is-invalid @enderror"
                            value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
                        @error('tanggal_transaksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8 form-group">
                        <label>Keterangan / Deskripsi</label>
                        <input type="text" name="keterangan"
                            class="form-control @error('keterangan') is-invalid @enderror" value="{{ old('keterangan') }}"
                            required>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <hr>
                <h5 class="mb-3">Detail Entri Jurnal</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="40%">Akun</th>
                            <th width="25%">Debit</th>
                            <th width="25%">Kredit</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="entri-rows">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right">Total</th>
                            <th class="text-right" id="total-debit">Rp 0</th>
                            <th class="text-right" id="total-kredit">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-success btn-sm mt-2" id="tambah-entri">Tambah Baris</button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
            </div>
        </form>
    </div>

    <table style="display: none;">
        <tbody id="entri-template">
            <tr class="entri-item">
                <td>
                    <select class="form-control entri-akun" required>
                        <option value="">-- Pilih Akun --</option>
                        @foreach ($akuns as $akun)
                            <option value="{{ $akun->id }}">{{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" class="form-control entri-debit" placeholder="0" min="0" step="any">
                </td>
                <td><input type="number" class="form-control entri-kredit" placeholder="0" min="0" step="any">
                </td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-entri">Hapus</button></td>
            </tr>
        </tbody>
    </table>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let entriIndex = 0;
            const tableBody = document.getElementById('entri-rows');
            const template = document.getElementById('entri-template');
            const totalDebitEl = document.getElementById('total-debit');
            const totalKreditEl = document.getElementById('total-kredit');

            function updateTotal() {
                let totalDebit = 0;
                let totalKredit = 0;
                document.querySelectorAll('#entri-rows .entri-item').forEach(function(row) {
                    totalDebit += parseFloat(row.querySelector('.entri-debit').value) || 0;
                    totalKredit += parseFloat(row.querySelector('.entri-kredit').value) || 0;
                });
                totalDebitEl.textContent = 'Rp ' + totalDebit.toLocaleString('id-ID');
                totalKreditEl.textContent = 'Rp ' + totalKredit.toLocaleString('id-ID');

                if (totalDebit === totalKredit && totalDebit > 0) {
                    totalDebitEl.classList.remove('text-danger');
                    totalKreditEl.classList.remove('text-danger');
                } else {
                    totalDebitEl.classList.add('text-danger');
                    totalKreditEl.classList.add('text-danger');
                }
            }

            function addRow() {
                const clone = template.querySelector('.entri-item').cloneNode(true);
                clone.querySelector('.entri-akun').name = `entri[${entriIndex}][id_akun]`;
                clone.querySelector('.entri-debit').name = `entri[${entriIndex}][debit]`;
                clone.querySelector('.entri-kredit').name = `entri[${entriIndex}][kredit]`;
                tableBody.appendChild(clone);
                entriIndex++;
            }

            document.getElementById('tambah-entri').addEventListener('click', addRow);

            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('entri-debit') || e.target.classList.contains(
                        'entri-kredit')) {
                    updateTotal();
                }
            });

            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('hapus-entri')) {
                    e.target.closest('.entri-item').remove();
                    updateTotal();
                }
            });

            addRow();
            addRow();
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
