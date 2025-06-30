@extends('layout.main')
@section('title', 'Distribusi Bahan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6"><h1>Distribusi Bahan ke Outlet</h1></div>
    </div>
@endsection

@section('content')
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">Aksi</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#form-tambah-manual"><i class="fas fa-plus"></i> Tambah Manual</button>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-import"><i class="fas fa-file-excel"></i> Import dari Excel</button>
            </div>
        </div>
        <div id="form-tambah-manual" class="collapse @if($errors->any() && !session('import_error')) show @endif">
            <form action="{{ route('distribusi.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Tanggal Distribusi</label><input type="date" name="tanggal_distribusi" class="form-control" value="{{ old('tanggal_distribusi', date('Y-m-d')) }}" required></div>
                        <div class="col-md-6 form-group"><label>Distribusi ke Outlet</label><select name="id_outlet_tujuan" class="form-control" required><option value="">-- Pilih Outlet Tujuan --</option>@foreach ($outlets as $outlet)<option value="{{ $outlet->id }}" {{ old('id_outlet_tujuan') == $outlet->id ? 'selected' : '' }}>{{ $outlet->nama_outlet }}</option>@endforeach</select></div>
                    </div>
                    <hr><h5>Detail Bahan</h5>
                    <div id="bahan-container"></div>
                    <button type="button" class="btn btn-success btn-sm" id="tambah-bahan">Tambah Bahan</button>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Distribusi</button></div>
            </form>
        </div>
    </div>

    <div id="bahan-template" style="display: none;">
        <div class="row bahan-item mb-2"><div class="col-md-6"><select class="form-control bahan-select" required><option value="">-- Pilih Bahan Baku --</option>@foreach ($bahanBaku as $bahan)<option value="{{ $bahan->id }}" data-stok="{{ $bahan->stok_tersedia }}" data-satuan="{{ $bahan->satuan }}">{{ $bahan->nama_bahan }}</option>@endforeach</select></div><div class="col-md-4"><input type="number" class="form-control" placeholder="Jumlah" min="1" step="any" required><small class="form-text text-muted stok-info"></small></div><div class="col-md-2"><button type="button" class="btn btn-danger btn-block hapus-bahan">Hapus</button></div></div>
    </div>

    {{-- MODAL IMPORT --}}
    <div class="modal fade" id="modal-import" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title">Import Distribusi</h4><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
                <form action="{{ route('distribusi.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Petunjuk:</strong><br>- Download template excel, lalu isi data sesuai format.<br>- Pastikan kolom <strong>nama_bahan</strong> diisi dengan nama yang sudah terdaftar di sistem.<br>- Kolom header di file excel adalah: <strong>nama_bahan, jumlah</strong>.<br><br>
                            <a href="{{ asset('public/templates/template_distribusi.xlsx') }}" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Download Template</a>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Tanggal Distribusi</label><input type="date" name="tanggal_distribusi" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            <div class="col-md-6 form-group"><label>Outlet Tujuan</label><select name="id_outlet_tujuan" class="form-control" required><option value="">-- Pilih Outlet --</option>@foreach ($outlets as $outlet)<option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>@endforeach</select></div>
                        </div>
                        <div class="form-group">
                            <label>Pilih File Excel (.xls, .xlsx)</label>
                            <div class="custom-file"><input type="file" name="import_file" class="custom-file-input" id="import_file" required><label class="custom-file-label" for="import_file">Pilih file...</label></div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between"><button type="button" class="btn btn-default" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Import</button></div>
                </form>
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
            container.addEventListener('click', function(e) { if (e.target.classList.contains('hapus-bahan')) e.target.closest('.bahan-item').remove(); });
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('bahan-select')) {
                    const option = e.target.options[e.target.selectedIndex];
                    const stokInfo = e.target.closest('.bahan-item').querySelector('.stok-info');
                    stokInfo.textContent = option.value ? `Stok tersedia: ${option.dataset.stok} ${option.dataset.satuan}` : '';
                }
            });
            document.getElementById('import_file').addEventListener('change', function(e){ e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : "Pilih file..."; });
        });
    </script>
     @if (session('add_sukses'))<script>var Toast=Swal.mixin({toast:!0,position:'top-end',showConfirmButton:!1,timer:3e3});Toast.fire({icon:'success',title:' &nbsp; {{ session("add_sukses") }}'})</script>@endif
     @if (session('error'))<script>var Toast=Swal.mixin({toast:!0,position:'top-end',showConfirmButton:!1,timer:4e3});Toast.fire({icon:'error',title:' &nbsp; {{ session("error") }}'})</script>@endif
@endsection