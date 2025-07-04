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
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">Formulir Tambah Pembelian Baru</h3>
        </div>
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tanggal Pembelian</label>
                        <input type="date" name="tanggal_pembelian"
                            class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                            value="{{ old('tanggal_pembelian', date('Y-m-d')) }}" required>
                        @error('tanggal_pembelian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control @error('id_supplier') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('id_supplier') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                        @error('id_supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nomor Invoice (Opsional)</label>
                        <input type="text" name="nomor_invoice"
                            class="form-control @error('nomor_invoice') is-invalid @enderror"
                            value="{{ old('nomor_invoice') }}">
                        @error('nomor_invoice')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                            <option value="Kredit" {{ old('metode_pembayaran') == 'Kredit' ? 'selected' : '' }}>Kredit/Tempo
                            </option>
                            <option value="Tunai" {{ old('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai
                            </option>
                            <option value="Digital/Bank"
                                {{ old('metode_pembayaran') == 'Digital/Bank' ? 'selected' : '' }}>Digital/Bank</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group" id="akun-pembayaran-div" style="display: none;">
                        <label>Bayar Dari Akun</label>
                        <select name="id_akun_pembayaran" class="form-control">
                            <option value="2">Kas di Tangan (Kas Kecil)</option>
                            <option value="1">Kas di Bank</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group" id="jatuh-tempo-pembelian-div">
                        <label>Tanggal Jatuh Tempo</label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control"
                            value="{{ old('tanggal_jatuh_tempo') }}">
                    </div>
                </div>
                <hr>
                <h5>Detail Bahan yang Dibeli</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Bahan Baku</th>
                            <th width="20%">Jumlah</th>
                            <th width="25%">Subtotal (Rp)</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bahan-rows"></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Total Biaya</th>
                            <th colspan="2" id="total-biaya" class="text-right">Rp 0</th>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-success btn-sm mt-2" id="tambah-bahan">Tambah Bahan</button>
            </div>
            <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Pembelian</button></div>
        </form>
    </div>

    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title">Riwayat Pembelian</h3>
            <div class="card-tools">
                @php
                    $queryParams = [
                        'id_supplier' => $supplier_id_terpilih,
                        'tanggal_mulai' => $tanggal_mulai,
                        'tanggal_selesai' => $tanggal_selesai,
                    ];
                @endphp
                <a href="{{ route('pembelian.create', array_merge($queryParams, ['export' => 'excel'])) }}"
                    class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
                <a href="{{ route('pembelian.create', array_merge($queryParams, ['export' => 'pdf'])) }}"
                    class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('pembelian.create') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 form-group"><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai"
                            class="form-control" value="{{ $tanggal_mulai }}" required></div>
                    <div class="col-md-4 form-group"><label>Tanggal Selesai</label><input type="date"
                            name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}" required></div>
                    <div class="col-md-3 form-group">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control">
                            <option value="">-- Semua Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ $supplier->id == $supplier_id_terpilih ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 form-group"><button type="submit"
                            class="btn btn-primary btn-block">Filter</button></div>
                </div>
            </form>
            <hr>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Tanggal</th>
                        <th>No. Invoice</th>
                        <th>Supplier</th>
                        <th class="text-center">Jml. Item</th>
                        <th class="text-right">Total Biaya</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                @forelse ($pembelians as $item)
                    <tbody>
                        <tr data-toggle="collapse" data-target="#detail-{{ $item->id }}" style="cursor: pointer;">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d F Y') }}</td>
                            <td>{{ $item->nomor_invoice }}</td>
                            <td>{{ $item->nama_supplier }}</td>
                            <td class="text-center">{{ $item->jumlah_item ?? 0 }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                            <td><span
                                    class="badge {{ $item->status == 'Lunas' ? 'badge-success' : 'badge-warning' }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-center"><button class="btn btn-xs btn-info"><i class="fas fa-eye"></i>
                                    Detail</button></td>
                        </tr>
                        <tr>
                            <td colspan="8" class="p-0" style="border-top: none;">
                                <div id="detail-{{ $item->id }}" class="collapse">
                                    <div class="p-3">
                                        <h6 class="text-bold">Rincian Bahan Baku:</h6>
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nama Bahan</th>
                                                    <th class="text-right">Jumlah</th>
                                                    <th class="text-right">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($groupedDetails[$item->id]))
                                                    @foreach ($groupedDetails[$item->id] as $detail)
                                                        <tr>
                                                            <td>{{ $detail->nama_bahan }}</td>
                                                            <td class="text-right">
                                                                {{ rtrim(rtrim(number_format($detail->jumlah, 2, ',', '.'), '0'), ',') }}
                                                                {{ $detail->satuan }}</td>
                                                            <td class="text-right">Rp
                                                                {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pembelian pada periode yang dipilih.
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
    </div>

    <table style="display: none;">
        <tbody id="bahan-template">
            <tr class="bahan-item">
                <td><select class="form-control bahan-select" required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        @foreach ($bahanBaku as $bahan)
                            <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">
                                {{ $bahan->nama_bahan }}</option>
                        @endforeach
                    </select></td>
                <td>
                    <div class="input-group"><input type="number" class="form-control jumlah-input"
                            placeholder="Jumlah" min="0.01" step="any" required>
                        <div class="input-group-append"><span class="input-group-text satuan-text"></span></div>
                    </div>
                </td>
                <td><input type="number" class="form-control subtotal-input" placeholder="Subtotal" min="0"
                        required></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm hapus-bahan">Hapus</button>
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
            const metodePembayaran = document.getElementById('metode_pembayaran');
            const akunPembayaranDiv = document.getElementById('akun-pembayaran-div');
            const jatuhTempoDiv = document.getElementById('jatuh-tempo-pembelian-div');

            function toggleFields() {
                const isKredit = metodePembayaran.value === 'Kredit';
                akunPembayaranDiv.style.display = isKredit ? 'none' : 'block';
                jatuhTempoDiv.style.display = isKredit ? 'block' : 'none';
            }

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
                }
            });

            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('bahan-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const satuan = selectedOption.getAttribute('data-satuan') || '';
                    e.target.closest('.bahan-item').querySelector('.satuan-text').textContent = satuan;
                }
            });

            metodePembayaran.addEventListener('change', toggleFields);
            toggleFields();
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
