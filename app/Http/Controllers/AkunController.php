<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    public function index()
    {
        $akuns = DB::table('akun')
            ->orderBy('kategori')
            ->orderBy('nama_akun')
            ->get()
            ->groupBy('kategori');

        return view('akun.index', ['akunsGrouped' => $akuns]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_akun' => 'required|string|max:100|unique:akun,nama_akun',
            'kategori' => 'required|in:Aset,Liabilitas,Ekuitas,Pendapatan,Beban Pokok Penjualan,Beban Operasional',
        ]);

        $saldo_normal = 'Kredit';
        if (in_array($request->kategori, ['Aset', 'Beban Pokok Penjualan', 'Beban Operasional'])) {
            $saldo_normal = 'Debit';
        }

        DB::table('akun')->insert([
            'nama_akun' => $request->nama_akun,
            'kategori' => $request->kategori,
            'saldo_normal' => $saldo_normal,
        ]);

        return redirect()->route('akun.index')->with('add_sukses', 'Akun baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_akun' => ['required', 'string', 'max:100', Rule::unique('akun')->ignore($id)],
        ]);

        DB::table('akun')->where('id', $id)->update([
            'nama_akun' => $request->nama_akun,
        ]);

        return redirect()->route('akun.index')->with('edit_sukses', 'Nama akun berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $isUsed = DB::table('jurnal_detail')->where('id_akun', $id)->exists();

        if ($isUsed) {
            return redirect()->route('akun.index')->with('error', 'Gagal menghapus! Akun ini sudah digunakan dalam transaksi jurnal.');
        }

        DB::table('akun')->where('id', $id)->delete();

        return redirect()->route('akun.index')->with('delete_sukses', 'Akun berhasil dihapus.');
    }
}
