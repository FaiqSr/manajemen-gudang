<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = DB::table('tbl_user')->where('email', $request->email)->first();

        if (Hash::check(@$request->password, @$user->password)) {
            $data = DB::table('tbl_user')->where('id', $user->id)->first();
            session([
                'berhasil_login' => true,
                'id_user' => $data->id,
                'id_role' => $data->id_role,
                'ID1' => null,
                'ID2' => null,
                'nilai' => null,
            ]);

            //return redirect('dashboard');
            $now = date('Y-m-d');
            $cek = date('Y-12-d');
            if ($now >= $cek) {
                return redirect('/')->with('gagal', 'Gagal');
            } else {
                return redirect('dashboard');
            }
        } else {
            return redirect('/')->with('gagal', 'Gagal');
        }
    }

    public function logout()
    {
        session()->forget(['id_user', 'id_role', 'ID1', 'ID2', 'nilai']);
        session()->flush();
        return redirect('/')->with('logout', 'Sukses');
    }
}
