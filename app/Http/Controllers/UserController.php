<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $user = DB::table('tbl_user')
            ->join('tbl_role', 'tbl_role.id', '=', 'tbl_user.id_role')
            ->select('tbl_user.*', 'tbl_role.nama_role')
            ->orderby('tbl_user.nama_lengkap', 'asc')
            ->get();

        return view('user.index', compact('user'));
    }
}
