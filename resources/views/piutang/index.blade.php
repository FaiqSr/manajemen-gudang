@extends('layout.main')
@section('title', 'Daftar Piutang Usaha')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Daftar Piutang Usaha</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Piutang Belum Lunas</h3>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Tanggal Penjualan</th>
                        <th>Jatuh Tempo</th>
                        <th>Nama Pelanggan</th>
                        <th>Outlet</th>
                        <th class="text-right">Sisa Tagihan</th>
                        <th class="text-center" width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($piutangs as $item)
                        @php
                            $jatuhTempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                            $isOverdue = $jatuhTempo->isPast() && $item->status != 'Lunas';
                        @endphp
                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}</td>
                            <td>
                                {{ $jatuhTempo->format('d M Y') }}
                                @if ($isOverdue)
                                    <span class="badge badge-danger">{{ $jatuhTempo->diffForHumans() }}</span>
                                @endif
                            </td>
                            <td>{{ $item->nama_pelanggan }}</td>
                            <td>{{ $item->nama_outlet }}</td>
                            <td class="text-right">Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <a href="{{ route('piutang.terima.create', $item->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fas fa-hand-holding-usd"></i> Terima Pembayaran
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada piutang yang belum dibayar.</td>
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
@endsection
