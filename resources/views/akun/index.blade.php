@extends('layout.main')
@section('title', 'Daftar Akun')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Daftar Akun (Chart of Accounts)</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Semua Akun</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-tambah">
                    <i class="fas fa-plus"></i> Tambah Akun Baru
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($akunsGrouped as $kategori => $akuns)
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ $kategori }}</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Akun</th>
                                            <th width="120px" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($akuns as $item)
                                            <tr>
                                                <td>{{ $item->nama_akun }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-xs btn-warning btn-edit"
                                                        data-id="{{ $item->id }}" data-nama="{{ $item->nama_akun }}"
                                                        title="Edit"><i class="fas fa-edit"></i></button>
                                                    <button onclick="del('{{ $item->id }}')"
                                                        class="btn btn-xs btn-danger" title="Hapus"><i
                                                            class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-tambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Akun Baru</h4><button type="button" class="close"
                        data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('akun.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Akun</label>
                            <input type="text" name="nama_akun" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori Akun</label>
                            <select name="kategori" class="form-control" required>
                                <option value="Aset">Aset</option>
                                <option value="Liabilitas">Liabilitas</option>
                                <option value="Ekuitas">Ekuitas</option>
                                <option value="Pendapatan">Pendapatan</option>
                                <option value="Beban Pokok Penjualan">Beban Pokok Penjualan</option>
                                <option value="Beban Operasional">Beban Operasional</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Nama Akun</h4><button type="button" class="close"
                        data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="form-edit" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_akun_edit">Nama Akun</label>
                            <input type="text" name="nama_akun" id="nama_akun_edit" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#nama_akun_edit').val(nama);
            $('#form-edit').attr('action', "{{ url('/akun/update') }}/" + id);
            $('#modal-edit').modal('show');
        });

        function del(id) {
            Swal.fire({
                title: "Ingin Menghapus Akun ini?",
                text: "Akun yang sudah digunakan dalam transaksi tidak dapat dihapus.",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('/akun/delete') }}/" + id;
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
    @if (session('error') || $errors->any())
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
            Toast.fire({
                icon: 'error',
                title: ' &nbsp; {{ session('error') ?: 'Gagal! Periksa kembali input Anda.' }}'
            });
        </script>
    @endif
@endsection
