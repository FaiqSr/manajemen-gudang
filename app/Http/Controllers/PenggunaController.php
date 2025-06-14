<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $data = DB::table('tbl_user')
            ->join('tbl_role', 'tbl_role.id', '=', 'tbl_user.id_role')
            ->select('tbl_user.*', 'tbl_role.nama_role')
            ->orderby('tbl_user.nama_lengkap', 'asc')
            ->get();

        return view('pengguna.index', ['data' => $data]);
    }

    public function create()
    {
        $role = DB::table('tbl_role')->get();

        return view('pengguna.add', ['role' => $role]);
    }

    public function add(Request $request)
    {
        DB::table('tbl_user')->insert([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'id_gender' => $request->id_gender,
            'id_role' => $request->id_role,
            'is_active' => '1',
            'password' => Hash::make($request->password),
        ]);

        return redirect('pengguna/index')->with('add_sukses', 1);
    }

    public function edit($id)
    {
        $row = DB::table('tbl_user')->where('tbl_user.id', $id)->first();
        $role = DB::table('tbl_role')->get();

        return view('pengguna.edit', [
            'row' => $row,
            'role' => $role,
        ]);
    }

    public function update(Request $request)
    {
        DB::table('tbl_user')
            ->where('id', $request->id)
            ->update([
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'id_gender' => $request->id_gender,
                'id_role' => $request->id_role,
                'password' => Hash::make($request->password),
            ]);

        return redirect('pengguna/index')->with('edit_sukses', 1);
    }

    public function delete($id)
    {
        DB::table('tbl_user')->where('id', $id)->delete();
        return redirect()->back()->with('delete_sukses', 1);
    }
}
