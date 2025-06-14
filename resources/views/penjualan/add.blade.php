@extends('layout.main')

@section('title', 'User')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>User</h1>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="ml-auto">
                                <h5>Tambah Data</h5>
                            </div>
                        </div>
                        <div class="col mr-auto">
                            <div class="mr-auto float-right">
                                <a href="{{ url('penjualan/' . request()->id) }}" class="btn btn-default">
                                    << Go Back to List </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <form action="{{ url('penjualan/' . request()->id . '/add') }}" method="post">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="idOutlet">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Produk</label>
                                    <select name="idProduk" id="idProduk" class="form-control" required>
                                        <option value="">--Pilih Produk--</option>
                                        @foreach ($produk as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal">Tanggal Penjualan</label>
                                    <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" class="form-control" name="jumlah" id="jumlah" required
                                        max="">
                                    <small id="maxInfo" class="form-text text-muted"></small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const bahanSelect = document.getElementById("idBahan");
            const jumlahInput = document.getElementById("jumlah");
            const maxInfo = document.getElementById("maxInfo");

            bahanSelect.addEventListener("change", function() {
                const selectedOption = bahanSelect.options[bahanSelect.selectedIndex];
                const maxJumlah = selectedOption.getAttribute("data-jumlah");

                if (maxJumlah) {
                    jumlahInput.setAttribute("max", maxJumlah);
                    maxInfo.textContent = `Maksimal jumlah: ${maxJumlah}`;
                } else {
                    jumlahInput.removeAttribute("max");
                    maxInfo.textContent = "";
                }
            });
        });
    </script>
@endsection
