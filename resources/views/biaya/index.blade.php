@extends('layout.main')
@section('title', 'Hutang Biaya Operasional')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Hutang Biaya Operasional</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tagihan Belum Lunas</h3>
            <div class="card-tools"><a href="{{ route('biaya.create') }}" class="btn btn-success btn-sm"><i
                        class="fas fa-plus"></i> Catat Tagihan Baru</a></div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Tgl Tagihan</th>
                        <th>Outlet</th>
                        <th>Jenis Biaya</th>
                        <th>Keterangan</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($biayaBelumLunas as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_tagihan)->format('d M Y') }}</td>
                            <td>{{ $item->nama_outlet }}</td>
                            <td>{{ $item->jenis_biaya }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="text-right">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center"><a href="{{ route('biaya.bayar.create', $item->id) }}"
                                    class="btn btn-xs btn-primary"><i class="fas fa-money-bill-wave"></i> Bayar</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada hutang biaya operasional.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    @if (session('add_sukses'))
        <script>
            var Toast = Swal.mixin({
                toast: !0,
                position: 'top-end',
                showConfirmButton: !1,
                timer: 3e3
            });
            Toast.fire({
                icon: 'success',
                title: ' &nbsp; {{ session('add_sukses') }}'
            })
        </script>
    @endif
@endsection
