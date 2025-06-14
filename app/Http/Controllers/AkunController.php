<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    public function index()
    {
        $data = DB::table('akun')->get();

        return view('akun.index', compact('data'));
    }

    public function add()
    {
        return view('akun.add');
    }

    public function create(Request $req)
    {
        DB::table('akun')->insert([
            'nama_akun' => $req->nama,
            'kategori' => $req->kategori,
            'saldo_normal' => $req->saldo
        ]);

        return redirect('akun')->with('add_sukses', 1);
    }
}
