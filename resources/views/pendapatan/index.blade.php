@extends('layout.main')
@section('title', 'Laporan Pendapatan')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Laporan Pendapatan</h1>
        </div>
        <div class="col-sm-6">
            {{-- Bagian ini bisa diisi breadcrumb jika perlu --}}
        </div>
    </div>
@endsection

@section('content')
    {{-- Card untuk Filter Laporan --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('pendapatan/') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pilih Outlet</label>
                            {{-- Controller harus mengirimkan variabel $outlets yang berisi semua data outlet --}}
                            <select name="outlet_id" class="form-control" required>
                                <option value="">-- Semua Outlet --</option>
                                @foreach ($outlets as $outlet)
                                    {{-- Menampilkan outlet yang sedang dipilih --}}
                                    <option value="{{ $outlet->id }}"
                                        {{ $outlet->id == $outlet_id_terpilih ? 'selected' : '' }}>
                                        {{ $outlet->nama_outlet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            {{-- Menampilkan tanggal yang sedang dipilih --}}
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggal_mulai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            {{-- Menampilkan tanggal yang sedang dipilih --}}
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggal_selesai }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Tampilkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Card untuk Hasil Laporan --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hasil Laporan Pendapatan</h3>
        </div>
        <div class="card-body">
            <table id="table1" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="20px">NO</th>
                        <th>Sumber Pendapatan</th>
                        <th class="text-right" width="200px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Inisialisasi variabel untuk menghitung grand total --}}
                    @php
                        $grand_total = 0;
                    @endphp

                    @forelse ($laporan as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_akun }}</td>
                            <td class="text-right">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                        </tr>
                        {{-- Menambahkan total item ke grand total --}}
                        @php
                            $grand_total += $item->total_pendapatan;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data pendapatan untuk periode dan outlet yang
                                dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Total Pendapatan</th>
                        <th class="text-right">Rp {{ number_format($grand_total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection

@section('script')
    {{-- Bagian script bisa diisi jika perlu inisialisasi library seperti DataTables atau lainnya --}}
    <script>
        // Contoh jika ingin menggunakan DataTables
        // $(function () {
        //   $("#table1").DataTable();
        // });
    </script>
@endsection
