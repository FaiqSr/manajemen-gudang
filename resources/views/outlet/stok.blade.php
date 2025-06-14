@extends('layout.main')

@section('title', 'Stok Outlet - ' . $outlet->nama_outlet)

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Stok Bahan Baku Outlet: {{ $outlet->nama_outlet }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('outlet') }}">Outlet</a></li>
                <li class="breadcrumb-item active">Stok {{ $outlet->nama_outlet }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ url('outlet') }}" class="btn btn-default">
                <i class="fas fa-reply"></i> Kembali ke Daftar Outlet
            </a>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Nama Bahan</th>
                        <th>Jumlah Stok</th>
                        <th>Satuan</th>
                        <th class="text-center" width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_bahan }}</td>
                            <td>{{ $item->jumlah_stok }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td class="text-center">
                                <a href="{{ route('outlet.stok.edit', ['stok_outlet_id' => $item->stok_outlet_id]) }}"
                                    class="btn btn-xs btn-warning" title="Edit Stok">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data stok untuk outlet ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        @if (session('edit_sukses'))
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('edit_sukses') }}'
            });
        @endif
        @if (session('error'))
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            Toast.fire({
                icon: 'error',
                title: ' &nbsp; {{ session('error') }}'
            });
        @endif
    </script>
@endsection
