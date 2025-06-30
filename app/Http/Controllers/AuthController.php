<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = DB::table('tbl_user')->where('email', $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect('/')->with('gagal', 'Email atau Password salah.');
        }

        if ($user->is_active != 1) {
            return redirect('/')->with('gagal', 'Akun Anda tidak aktif.');
        }

        session([
            'berhasil_login' => true,
            'is_login' => true,
            'id_user' => $user->id,
            'id_role' => $user->id_role,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email,
        ]);

        $now = date('Y-m-d');
        $cek = date('Y-12-d');
        if ($now >= $cek) {
            session()->flush();
            return redirect('/')->with('gagal', 'Aplikasi telah kadaluarsa.');
        }

        return redirect('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerateToken();
        return redirect('/')->with('logout', 'Anda telah berhasil logout.');
    }
}
