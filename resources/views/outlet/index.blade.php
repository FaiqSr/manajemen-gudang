@extends('layout.main')

@section('title', 'Outlet')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Outlet</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ url('outlet/add') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Data</a>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Outlet</th>
                        <th>Telpon</th>
                        <th>Alamat</th>
                        <th>PIC</th>
                        <th class="text-center" width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($outlet as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_outlet }}</td>
                            <td>{{ $item->telepon }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>{{ $item->pic }}</td>
                            <td class="text-center">
                                <a href="{{ url('outlet/stok/' . $item->id) }}" class="btn btn-xs btn-primary"
                                    title="Edit">Stok Bahan</a>
                                <div>
                                    <a href="{{ url('outlet/edit/' . $item->id) }}" class="btn btn-xs btn-warning"
                                        title="Edit"><i class="fas fa-edit"></i> </a>
                                    <button onclick="del({{ $item->id }})" class="btn btn-xs btn-danger"
                                        title="Hapus"><i class="fas fa-trash"></i> </button>
                                </div>
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
        function add_sukses() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });

            Toast.fire({
                icon: 'success',
                title: ' &nbsp; Tambah Data Berhasil'
            });
        }

        function edit_sukses() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });

            Toast.fire({
                icon: 'success',
                title: ' &nbsp; Update Data Berhasil'
            });
        }

        function delete_sukses() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });

            Toast.fire({
                icon: 'success',
                title: ' &nbsp; Hapus Data Berhasil'
            });
        }

        function del(id) {
            Swal.fire({
                title: "Ingin Menghapus Data ini?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('outlet/delete') }}/" + id;
                }
            });
        }
    </script>

    @if (session('add_sukses'))
        <script>
            add_sukses();
        </script>
    @endif

    @if (session('edit_sukses'))
        <script>
            edit_sukses();
        </script>
    @endif

    @if (session('delete_sukses'))
        <script>
            delete_sukses();
        </script>
    @endif
@endsection
