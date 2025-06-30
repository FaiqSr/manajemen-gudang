@extends('layout.main')
@section('title', 'Stok Opname')

@section('breadcrums')
<div class="row mb-2">
    <div class="col-sm-6"><h1>Input Stok Opname</h1></div>
</div>
@endsection

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Aksi</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#form-input-manual">
                <i class="fas fa-edit"></i> Input Manual
            </button>
            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-import">
                <i class="fas fa-file-excel"></i> Import dari Excel
            </button>
        </div>
    </div>
    <div id="form-input-manual" class="collapse @if($errors->any() && !session('import_error')) show @endif">
        <div class="card-body border-top">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Pilih Outlet</label>
                    <select id="id_outlet_manual" class="form-control" required>
                        <option value="">-- Pilih Outlet --</option>
                        @foreach ($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Tanggal Stok Opname</label>
                    <input type="date" id="tanggal_opname_manual" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="stok-opname-area" style="display: none;">
    <div class="card-header"><h3 class="card-title">Daftar Stok Bahan Baku</h3></div>
    <form action="{{ route('stok_opname.store') }}" method="POST">
        @csrf
        <input type="hidden" name="id_outlet" id="hidden_id_outlet">
        <input type="hidden" name="tanggal_opname" id="hidden_tanggal_opname">
        <div class="card-body p-0">
            <table class="table table-bordered">
                <thead><tr><th>Nama Bahan</th><th width="20%">Stok Sistem</th><th width="20%">Stok Fisik</th><th width="20%">Selisih</th></tr></thead>
                <tbody id="opname-rows"></tbody>
            </table>
        </div>
        <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan Hasil Stok Opname</button></div>
    </form>
</div>
<div id="loading-area" style="display: none;" class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>

{{-- MODAL IMPORT --}}
<div class="modal fade" id="modal-import" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Import Stok Opname</h4><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form action="{{ route('stok_opname.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Petunjuk:</strong><br>- Download template, lalu isi data sesuai format.<br>- Kolom header di file excel adalah: <strong>nama_bahan, stok_fisik</strong>.<br><br>
                        <a href="{{ asset('public/templates/template_stok_opname.xlsx') }}" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Download Template</a>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Outlet</label><select name="id_outlet" class="form-control" required><option value="">-- Pilih Outlet --</option>@foreach($outlets as $outlet)<option value="{{$outlet->id}}">{{$outlet->nama_outlet}}</option>@endforeach</select></div>
                        <div class="col-md-6 form-group"><label>Tanggal Stok Opname</label><input type="date" name="tanggal_opname" class="form-control" value="{{date('Y-m-d')}}" required></div>
                    </div>
                    <div class="form-group"><label>Pilih File Excel (.xls, .xlsx)</label><div class="custom-file"><input type="file" name="import_file" class="custom-file-input" id="import_file" required><label class="custom-file-label" for="import_file">Pilih file...</label></div></div>
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
    const outletSelect = document.getElementById('id_outlet_manual');
    const opnameArea = document.getElementById('stok-opname-area');
    const loadingArea = document.getElementById('loading-area');
    const tableBody = document.getElementById('opname-rows');
    const hiddenOutletId = document.getElementById('hidden_id_outlet');
    const hiddenTanggalOpname = document.getElementById('hidden_tanggal_opname');
    const tanggalOpnameManual = document.getElementById('tanggal_opname_manual');

    function fetchStokData() {
        const outletId = outletSelect.value;
        tableBody.innerHTML = '';
        opnameArea.style.display = 'none';
        if (!outletId) return;

        hiddenOutletId.value = outletId;
        hiddenTanggalOpname.value = tanggalOpnameManual.value;
        loadingArea.style.display = 'block';

        fetch(`{{ route('get-bahan-by-outlet') }}?id_outlet=${outletId}`)
            .then(response => response.json()).then(data => {
                loadingArea.style.display = 'none';
                if (data.length > 0) opnameArea.style.display = 'block';
                data.forEach((item, index) => {
                    const row = tableBody.insertRow();
                    row.innerHTML = `<td>${item.nama_bahan}<input type="hidden" name="items[${index}][id_bahan]" value="${item.id}"></td><td><input type="number" class="form-control-plaintext" name="items[${index}][stok_sistem]" value="${item.stok_sistem}" readonly></td><td><input type="number" name="items[${index}][stok_fisik]" class="form-control stok-fisik" value="${item.stok_sistem}" min="0" step="any" required></td><td class="selisih text-center font-weight-bold">0</td>`;
                });
            });
    }

    outletSelect.addEventListener('change', fetchStokData);
    tanggalOpnameManual.addEventListener('change', () => { hiddenTanggalOpname.value = tanggalOpnameManual.value; });

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('stok-fisik')) {
            const row = e.target.closest('tr');
            const stokSistem = parseFloat(row.querySelector('input[name*="[stok_sistem]"]').value) || 0;
            const stokFisik = parseFloat(e.target.value) || 0;
            const selisih = stokFisik - stokSistem;
            const selisihEl = row.querySelector('.selisih');
            selisihEl.textContent = selisih;
            selisihEl.classList.remove('text-success', 'text-danger');
            if (selisih > 0) selisihEl.classList.add('text-success'); else if (selisih < 0) selisihEl.classList.add('text-danger');
        }
    });

    document.getElementById('import_file').addEventListener('change', function(e){ e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : "Pilih file..."; });
});
</script>
@if(session('add_sukses'))<script>var Toast=Swal.mixin({toast:!0,position:'top-end',showConfirmButton:!1,timer:3e3});Toast.fire({icon:'success',title:' &nbsp; {{ session("add_sukses") }}'})</script>@endif
@if(session('error'))<script>var Toast=Swal.mixin({toast:!0,position:'top-end',showConfirmButton:!1,timer:4e3});Toast.fire({icon:'error',title:' &nbsp; {{ session("error") }}'})</script>@endif
@endsection