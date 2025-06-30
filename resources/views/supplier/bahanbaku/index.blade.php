@extends('layout.main')
@section('title', 'Master Bahan Baku')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Master - Bahan Baku</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ url('bahan/add') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Data</a>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th class="text-right">Harga Pokok</th>
                        <th class="text-center" width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_bahan }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga_pokok ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('bahan.edit', $item->id) }}" class="btn btn-xs btn-warning"
                                    title="Edit"><i class="fas fa-edit"></i></a>
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
                title: "Ingin Menghapus Data ini?",
                text: "Pastikan bahan baku ini tidak digunakan dalam transaksi apapun.",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('bahan/delete') }}/" + id;
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
                title: ' &nbsp; Data berhasil ditambahkan!'
            });
        </script>
    @endif
    @if (session('edit_sukses'))
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; Data berhasil diperbarui!'
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
                title: ' &nbsp; Data berhasil dihapus!'
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
