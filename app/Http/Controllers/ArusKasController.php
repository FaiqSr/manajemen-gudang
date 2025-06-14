<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArusKasController extends Controller
{

    public function create()
    {
        // Ambil semua outlet untuk pilihan dropdown
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        // Ambil semua akun yang bisa menjadi sumber/tujuan transfer (akun kas atau bank)
        // Berdasarkan file akun.sql Anda, ini adalah id 1 (Kas di Bank) dan 2 (Kas di Tangan)
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('aruskas.index', [
            'outlets' => $outlets,
            'akunKasBank' => $akunKasBank
        ]);
    }

    /**
     * Menyimpan transaksi transfer kas ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transfer' => 'required|date',
            'akun_sumber' => 'required|integer',
            'akun_tujuan' => 'required|integer|different:akun_sumber',
            'outlet_id' => 'required_if:akun_sumber,2',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $tanggal = $request->input('tanggal_transfer');
                $keterangan = $request->input('keterangan', 'Transfer Kas Antar Akun');
                $jumlah = $request->input('jumlah');
                $akunTujuanId = $request->input('akun_tujuan');
                $akunSumberId = $request->input('akun_sumber');
                $outletId = $request->input('outlet_id');

                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $tanggal,
                    'keterangan' => $keterangan,
                    'created_at' => now()
                ]);
                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $akunTujuanId,
                    'id_outlet' => null,
                    'debit' => $jumlah,
                    'kredit' => 0
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $akunSumberId,
                    'id_outlet' => $outletId,
                    'debit' => 0,
                    'kredit' => $jumlah
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect('transfer-kas')->with('add_sukses', 'Transfer kas berhasil dicatat!');
    }
}
