<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = DB::table('produk')->orderBy('nama_produk')->get();
        return view('produk.index', ['produks' => $produks]);
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga_produksi' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        DB::table('produk')->insert([
            'nama_produk' => $request->nama_produk,
            'harga_produksi' => $request->harga_produksi,
            'harga_jual' => $request->harga_jual,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('produk.index')->with('add_sukses', 'Produk baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $produk = DB::table('produk')->where('id', $id)->first();
        if (!$produk) {
            abort(404);
        }
        return view('produk.edit', ['produk' => $produk]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga_produksi' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $produk = DB::table('produk')->where('id', $id)->first();
        if (!$produk) {
            abort(404);
        }

        DB::table('produk')->where('id', $id)->update([
            'nama_produk' => $request->nama_produk,
            'harga_produksi' => $request->harga_produksi,
            'harga_jual' => $request->harga_jual,
            'updated_at' => now(),
        ]);

        return redirect()->route('produk.index')->with('edit_sukses', 'Data produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $isUsed = DB::table('penjualan_detail')->where('id_produk', $id)->exists();

        if ($isUsed) {
            return redirect()->route('produk.index')->with('error', 'Gagal menghapus! Produk ini sudah pernah digunakan dalam transaksi penjualan.');
        }

        DB::table('produk')->where('id', $id)->delete();

        return redirect()->route('produk.index')->with('delete_sukses', 'Produk berhasil dihapus.');
    }
}
