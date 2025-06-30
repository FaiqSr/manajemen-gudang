@extends('layout.main')
@section('title', 'Daftar Hutang Usaha')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Daftar Hutang Usaha</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hutang Belum Lunas</h3>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Tgl Pembelian</th>
                        <th>Jatuh Tempo</th>
                        <th>No. Invoice</th>
                        <th>Supplier</th>
                        <th class="text-right">Sisa Tagihan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hutangs as $item)
                        @php
                            $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                            $isOverdue = $jatuhTempo->isPast() && $item->status != 'Lunas';
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}</td>
                            <td>
                                {{ $jatuhTempo->format('d M Y') }}
                                @if ($isOverdue)
                                    <span class="badge badge-danger">{{ $jatuhTempo->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td>{{ $item->nomor_invoice }}</td>
                            <td>{{ $item->nama_supplier }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('hutang.bayar.create', $item->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fas fa-money-bill-wave"></i> Bayar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada hutang yang perlu dibayar.</td>
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
