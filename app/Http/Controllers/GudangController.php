<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function stok()
    {
        $data = DB::table('stok_gudang_detail')->join('stok_gudang', 'stok_gudang.id', '=', 'stok_gudang_detail.id_stok_gudang')->join('bahan_baku', 'bahan_baku.id', '=', 'stok_gudang.id_bahan_baku')->select('bahan_baku.nama_bahan', 'stok_gudang_detail.*')->orderby('stok_gudang_detail.tanggal', 'desc')->get();

        return view('gudang.stok', ['data' => $data]);
    }

    public function stokTerkini()
    {
        $data = DB::table('stok_gudang')->join('bahan_baku', 'bahan_baku.id', '=', 'stok_gudang.id_bahan_baku')->select('bahan_baku.nama_bahan', 'stok_gudang.*')->get();

        return view('gudang.stok-terkini', compact('data'));
    }
}
