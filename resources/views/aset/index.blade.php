@extends('layout.main')
@section('title', 'Daftar Aset')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Manajemen Aset</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Total Aset</h3>
                    <div class="card-tools">
                        <a href="{{ route('aset.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Tambah Aset Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($asetGrouped as $namaOutlet => $assets)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><strong>{{ $namaOutlet ?: 'Aset Pusat (Tidak ada Outlet)' }}</strong></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Nama Aset</th>
                            <th>Jenis</th>
                            <th>Tgl Perolehan</th>
                            <th class="text-right">Harga Perolehan</th>
                            <th class="text-right">Penyusutan / Bulan</th>
                            <th class="text-center" width="80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $item)
                            <tr>
                                <td>{{ $item->nama_aset }} ({{ $item->kode_aset }})</td>
                                <td>{{ $item->jenis_aset }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d F Y') }}</td>
                                <td class="text-right">Rp {{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($item->penyusutan_per_bulan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <button onclick="del('{{ $item->id }}')" class="btn btn-xs btn-danger"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="3" class="text-right">Total Aset {{ $namaOutlet ?: 'Pusat' }}</th>
                            <th class="text-right">Rp {{ number_format($assets->sum('harga_perolehan'), 0, ',', '.') }}
                            </th>
                            <th class="text-right">Rp
                                {{ number_format($assets->sum('penyusutan_per_bulan'), 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach
@endsection

@section('script')
    <script>
        function del(id) {
            Swal.fire({
                title: "Ingin Menghapus Data Aset ini?",
                text: "Pastikan Anda yakin, data tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('aset/delete') }}/" + id;
                }
            });
        }
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
    @if (session('delete_sukses'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('delete_sukses') }}'
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
