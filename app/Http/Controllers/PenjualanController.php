<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $outlet = DB::table('outlets')->select('nama_outlet', 'id')->get();

        return view('penjualan.index', compact('outlet'));
    }

    public function show($id)
    {
        $data = DB::table('penjualan_detail')
            ->join('penjualan', 'penjualan_detail.id_penjualan', 'penjualan.id')
            ->join('produk', 'penjualan_detail.id_produk', 'produk.id')
            ->where('penjualan.id_outlet', $id)
            ->get();
        return view('penjualan.show', compact('data'));
    }

    public function add($id)
    {
        $produk = DB::table('produk')->select('id', 'nama_produk')->get();
        return view('penjualan.add', compact('produk', 'id'));
    }

    public function create(Request $req)
    {
        $produk = DB::table('produk')->where('id', $req->idProduk)->first();

        $penjualanId = DB::table('penjualan')->insertGetId([
            'id_outlet' => $req->idOutlet,
            'tanggal_penjualan' => $req->tanggal,
            'total_pendapatan' => $produk->harga_jual * $req->jumlah
        ]);

        DB::table('penjualan_detail')->insert([
            'id_penjualan' => $penjualanId,
            'id_produk' => $req->idProduk,
            'jumlah' => $req->jumlah,
            'harga_saat_transaksi' => $produk->harga_jual,
            'subtotal' => $produk->harga_jual * $req->jumlah
        ]);

        $jurnalId = DB::table('jurnal')->insertGetId([
            'tanggal_transaksi' => $req->tanggal,
            'referensi' => $penjualanId,
            'Keterangan' => "Penjualan Produk di Outlet",
        ]);

        DB::table('jurnal_detail')->insert([
            'id_jurnal' => $jurnalId,
            'id_akun' => 2,
            'id_outlet' => $req->idOutlet,
            'debit' => $produk->harga_jual * $req->jumlah
        ]);
        DB::table('jurnal_detail')->insert([
            'id_jurnal' => $jurnalId,
            'id_akun' => 14,
            'id_outlet' => $req->idOutlet,
            'kredit' => $produk->harga_jual * $req->jumlah
        ]);
        DB::table('jurnal_detail')->insert([
            'id_jurnal' => $jurnalId,
            'id_akun' => 16,
            'id_outlet' => $req->idOutlet,
            'debit' => $produk->harga_produksi * $req->jumlah
        ]);
        DB::table('jurnal_detail')->insert([
            'id_jurnal' => $jurnalId,
            'id_akun' => 4,
            'id_outlet' => $req->idOutlet,
            'kredit' => $produk->harga_jual * $req->jumlah
        ]);

        return redirect('penjualan/' . $req->idOutlet)->with('add_sukses', 1);
    }
}
