@extends('layout.main')
@section('title', 'Manajemen Produk')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Manajemen Produk</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Produk</h3>
            <div class="card-tools">
                <a href="{{ route('produk.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Tambah Produk Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Produk</th>
                        <th class="text-right">Harga Produksi (HPP)</th>
                        <th class="text-right">Harga Jual</th>
                        <th class="text-center" width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produks as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga_produksi, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-xs btn-warning"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="del('{{ $item->id }}')" class="btn btn-xs btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function del(id) {
            Swal.fire({
                title: "Ingin Menghapus Data Produk ini?",
                text: "Pastikan Anda yakin, data tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('produk/delete') }}/" + id;
                }
            });
        }
    </script>

    @if (session('add_sukses') || session('edit_sukses') || session('delete_sukses'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('add_sukses') ?: (session('edit_sukses') ?: session('delete_sukses')) }}'
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
