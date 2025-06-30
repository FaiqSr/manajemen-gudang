<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function create()
    {
        $roles = DB::table('tbl_role')->get();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:tbl_user,email',
            'password' => 'required|string|min:6|confirmed',
            'id_role' => 'required|integer|exists:tbl_role,id',
            'id_gender' => 'required|integer',
            'is_active' => 'required|integer',
        ]);

        DB::table('tbl_user')->insert([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $request->id_role,
            'id_gender' => $request->id_gender,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('user.index')->with('add_sukses', 'User baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = DB::table('tbl_user')->where('id', $id)->first();
        if (!$user) {
            abort(404);
        }
        $roles = DB::table('tbl_role')->get();
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:50', Rule::unique('tbl_user')->ignore($id)],
            'password' => 'nullable|string|min:6|confirmed',
            'id_role' => 'required|integer|exists:tbl_role,id',
            'id_gender' => 'required|integer',
            'is_active' => 'required|integer',
        ]);

        $updateData = [
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'id_role' => $request->id_role,
            'id_gender' => $request->id_gender,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('tbl_user')->where('id', $id)->update($updateData);

        return redirect()->route('user.index')->with('edit_sukses', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($id == session()->get('id_user')) {
            return redirect()->route('user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::table('tbl_user')->where('id', $id)->delete();

        return redirect()->route('user.index')->with('delete_sukses', 'User berhasil dihapus.');
    }
}
