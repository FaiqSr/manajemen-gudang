@extends('layout.main')
@section('title', 'Penjualan Bahan Baku')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Penjualan Bahan Baku Outlet</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">Formulir Input Penjualan</h3>
        </div>
        <form action="{{ route('penjualan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tanggal Penjualan</label>
                        <input type="date" name="tanggal_penjualan"
                            class="form-control @error('tanggal_penjualan') is-invalid @enderror"
                            value="{{ old('tanggal_penjualan', date('Y-m-d')) }}" required>
                        @error('tanggal_penjualan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Outlet Penjualan</label>
                        <select name="id_outlet" id="id_outlet"
                            class="form-control @error('id_outlet') is-invalid @enderror" required>
                            <option value="">-- Pilih Outlet --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('id_outlet') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->nama_outlet }}</option>
                            @endforeach
                        </select>
                        @error('id_outlet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            <option value="Tunai" @if (old('metode_pembayaran') == 'Tunai') selected @endif>Tunai</option>
                            <optgroup label="EDC">
                                <option value="EDC BCA" @if (old('metode_pembayaran') == 'EDC BCA') selected @endif>EDC BCA</option>
                                <option value="EDC BRI" @if (old('metode_pembayaran') == 'EDC BRI') selected @endif>EDC BRI</option>
                                <option value="EDC MANDIRI" @if (old('metode_pembayaran') == 'EDC MANDIRI') selected @endif>EDC MANDIRI
                                </option>
                                <option value="EDC BNI" @if (old('metode_pembayaran') == 'EDC BNI') selected @endif>EDC BNI</option>
                                <option value="EDC BSI" @if (old('metode_pembayaran') == 'EDC BSI') selected @endif>EDC BSI</option>
                                <option value="EDC BTN" @if (old('metode_pembayaran') == 'EDC BTN') selected @endif>EDC BTN</option>
                            </optgroup>
                            <optgroup label="QRIS">
                                <option value="QRIS BRI" @if (old('metode_pembayaran') == 'QRIS BRI') selected @endif>QRIS BRI</option>
                                <option value="QRIS BTN" @if (old('metode_pembayaran') == 'QRIS BTN') selected @endif>QRIS BTN
                                </option>
                                <option value="QRIS-QPON" @if (old('metode_pembayaran') == 'QRIS-QPON') selected @endif>QRIS-QPON
                                </option>
                            </optgroup>
                            <optgroup label="E-Wallet & Online">
                                <option value="DANA" @if (old('metode_pembayaran') == 'DANA') selected @endif>DANA</option>
                                <option value="DANA DINEIN" @if (old('metode_pembayaran') == 'DANA DINEIN') selected @endif>DANA DINE-IN
                                </option>
                                <option value="GOFOOD" @if (old('metode_pembayaran') == 'GOFOOD') selected @endif>GO-FOOD</option>
                                <option value="GRAB FOOD" @if (old('metode_pembayaran') == 'GRAB FOOD') selected @endif>GRAB-FOOD
                                </option>
                            </optgroup>
                            <optgroup label="Lainnya">
                                <option value="Kredit" @if (old('metode_pembayaran') == 'Kredit') selected @endif>Kredit/Piutang
                                </option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-6 form-group" id="nama_pelanggan_div" style="display: none;">
                        <label>Nama Pelanggan (Wajib jika Kredit)</label>
                        <input type="text" name="nama_pelanggan"
                            class="form-control @error('nama_pelanggan') is-invalid @enderror"
                            value="{{ old('nama_pelanggan') }}">
                        @error('nama_pelanggan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <hr>
                <h5>Detail Bahan yang Dijual</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th width="20%">Jumlah</th>
                            <th width="25%">Harga Jual (Rp)</th>
                            <th class="text-right" width="25%">Subtotal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bahan-rows"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total Pendapatan</th>
                            <th colspan="2" id="total-pendapatan" class="text-right">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-success btn-sm mt-2" id="tambah-bahan">Tambah Bahan</button>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Penjualan</button></div>
        </form>
    </div>

    {{-- Tabel Riwayat tidak ditampilkan di sini karena sudah ada di Laporan Penjualan --}}

    <table style="display: none;">
        <tbody id="bahan-template">
            <tr class="bahan-item">
                <td><select class="form-control bahan-select" required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach ($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }}</option>
                        @endforeach
                    </select></td>
                <td><input type="number" class="form-control jumlah-input" placeholder="Jumlah" min="1"
                        required><small class="form-text text-muted stok-info"></small></td>
                <td><input type="number" class="form-control harga-input" placeholder="Harga Jual" min="0" required>
                </td>
                <td class="text-right subtotal-text">Rp 0</td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-bahan">Hapus</button></td>
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
            const outletSelect = document.getElementById('id_outlet');

            function updateRow(row) {
                const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
                const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
                const subtotal = jumlah * harga;
                row.querySelector('.subtotal-text').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                updateTotal();
            }

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('#bahan-rows .bahan-item').forEach(function(row) {
                    const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
                    const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
                    total += jumlah * harga;
                });
                document.getElementById('total-pendapatan').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            document.getElementById('tambah-bahan').addEventListener('click', function() {
                if (!outletSelect.value) {
                    alert('Silakan pilih outlet terlebih dahulu!');
                    return;
                }
                const clone = template.querySelector('.bahan-item').cloneNode(true);
                clone.querySelector('.bahan-select').name = `bahan[${bahanIndex}][id]`;
                clone.querySelector('.jumlah-input').name = `bahan[${bahanIndex}][jumlah]`;
                clone.querySelector('.harga-input').name = `bahan[${bahanIndex}][harga]`;
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
                    const idBahanBaku = e.target.value;
                    const idOutlet = outletSelect.value;
                    const row = e.target.closest('.bahan-item');
                    const jumlahInput = row.querySelector('.jumlah-input');
                    const stokInfo = row.querySelector('.stok-info');

                    if (!idBahanBaku) {
                        stokInfo.textContent = '';
                        jumlahInput.removeAttribute('max');
                        return;
                    }
                    fetch(
                            `{{ route('get-stok.outlet') }}?id_outlet=${idOutlet}&id_bahan_baku=${idBahanBaku}`)
                        .then(response => response.json())
                        .then(data => {
                            const stok = data.stok || 0;
                            stokInfo.textContent = `Stok tersedia: ${stok}`;
                            jumlahInput.setAttribute('max', stok);
                        });
                }
            });

            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('jumlah-input') || e.target.classList.contains(
                        'harga-input')) {
                    updateRow(e.target.closest('.bahan-item'));
                }
            });

            const metodePembayaran = document.getElementById('metode_pembayaran');
            const namaPelangganDiv = document.getElementById('nama_pelanggan_div');

            function togglePelanggan() {
                namaPelangganDiv.style.display = (metodePembayaran.value === 'Kredit') ? 'block' : 'none';
            }
            metodePembayaran.addEventListener('change', togglePelanggan);
            togglePelanggan();
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
