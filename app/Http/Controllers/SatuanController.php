<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = DB::table('satuan')->orderBy('nama_satuan')->get();
        return view('satuan.index', ['satuans' => $satuans]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuan,nama_satuan',
        ]);

        DB::table('satuan')->insert([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return redirect()->route('satuan.index')->with('add_sukses', 'Satuan baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_satuan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('satuan')->ignore($id),
            ],
        ]);

        DB::table('satuan')->where('id', $id)->update([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return redirect()->route('satuan.index')->with('edit_sukses', 'Data satuan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $satuan = DB::table('satuan')->where('id', $id)->first();
        if (!$satuan) {
            return redirect()->route('satuan.index')->with('error', 'Satuan tidak ditemukan.');
        }

        $isUsed = DB::table('bahan_baku')->where('satuan', $satuan->nama_satuan)->exists();

        if ($isUsed) {
            return redirect()->route('satuan.index')->with('error', 'Gagal menghapus! Satuan ini sedang digunakan di data bahan baku.');
        }

        DB::table('satuan')->where('id', $id)->delete();

        return redirect()->route('satuan.index')->with('delete_sukses', 'Satuan berhasil dihapus.');
    }
}
