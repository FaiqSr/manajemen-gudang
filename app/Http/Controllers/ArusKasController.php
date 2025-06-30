<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArusKasController extends Controller
{

    public function create()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('aruskas.index', [
            'outlets' => $outlets,
            'akunKasBank' => $akunKasBank
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transfer' => 'required|date',
            'akun_sumber' => 'required|integer|exists:akun,id',
            'outlet_sumber_id' => 'required_if:akun_sumber,2|nullable|integer|exists:outlets,id',
            'akun_tujuan' => 'required|integer|exists:akun,id',
            'outlet_tujuan_id' => 'required_if:akun_tujuan,2|nullable|integer|exists:outlets,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($request->akun_sumber == $request->akun_tujuan && $request->outlet_sumber_id == $request->outlet_tujuan_id) {
            return redirect()->back()->withInput()->with('error', 'Akun sumber dan tujuan tidak boleh sama persis.');
        }

        try {
            DB::transaction(function () use ($request) {
                $keteranganJurnal = $request->keterangan ?: 'Transfer Kas Antar Akun';

                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_transfer,
                    'keterangan' => $keteranganJurnal,
                    'referensi' => 'transfer_kas',
                    'created_at' => now()
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->akun_tujuan,
                    'id_outlet' => $request->akun_tujuan == 2 ? $request->outlet_tujuan_id : null,
                    'debit' => $request->jumlah,
                    'kredit' => 0
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->akun_sumber,
                    'id_outlet' => $request->akun_sumber == 2 ? $request->outlet_sumber_id : null,
                    'debit' => 0,
                    'kredit' => $request->jumlah
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect()->route('transfer-kas')->with('add_sukses', 'Transfer kas berhasil dicatat!');
    }
}
