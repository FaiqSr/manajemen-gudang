@extends('layout.main')
@section('title', 'Data Satuan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Data Satuan</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Satuan</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-tambah">
                    <i class="fas fa-plus"></i> Tambah Satuan
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Satuan</th>
                        <th class="text-center" width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($satuans as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_satuan }}</td>
                            <td class="text-center">
                                <button class="btn btn-xs btn-warning btn-edit" data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_satuan }}" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="del('{{ $item->id }}')" class="btn btn-xs btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal-tambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Satuan Baru</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('satuan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_satuan_tambah">Nama Satuan</label>
                            <input type="text" name="nama_satuan" id="nama_satuan_tambah" class="form-control" required>
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
                    <h4 class="modal-title">Edit Satuan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-edit" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_satuan_edit">Nama Satuan</label>
                            <input type="text" name="nama_satuan" id="nama_satuan_edit" class="form-control" required>
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

            $('#nama_satuan_edit').val(nama);
            $('#form-edit').attr('action', "{{ url('/satuan/update') }}/" + id);
            $('#modal-edit').modal('show');
        });

        function del(id) {
            Swal.fire({
                title: "Ingin Menghapus Data Satuan ini?",
                text: "Pastikan Anda yakin, data tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('satuan/delete') }}/" + id;
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
                title: ' &nbsp; {{ session('error') ?: 'Terjadi kesalahan. Periksa kembali input Anda.' }}'
            });
        </script>
    @endif
@endsection
