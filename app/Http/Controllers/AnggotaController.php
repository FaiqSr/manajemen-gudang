<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index()
    {
        $data = DB::table('tbl_anggota')->get();

        return view('anggota.index', ['data' => $data]);
    }

    public function create()
    {
        return view('anggota.add');
    }

    public function add(Request $request)
    {
        DB::table('tbl_anggota')->insert([
            'namaanggota' => $request->namaanggota,
            'pangkat' => $request->pangkat,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
        ]);

        return redirect('anggota/index')->with('add_sukses', 1);
    }

    public function edit($id)
    {
        $row = DB::table('tbl_anggota')->where('tbl_anggota.id', $id)->first();

        return view('anggota.edit', [
            'row' => $row,
        ]);
    }

    public function update(Request $request)
    {
        DB::table('tbl_anggota')
            ->where('id', $request->id)
            ->update([
                'namaanggota' => $request->namaanggota,
                'pangkat' => $request->pangkat,
                'nip' => $request->nip,
                'jabatan' => $request->jabatan,
            ]);

        return redirect('anggota/index')->with('edit_sukses', 1);
    }

    public function delete($id)
    {
        DB::table('tbl_anggota')->where('id', $id)->delete();

        return redirect()->back()->with('delete_sukses', 1);
    }
}
