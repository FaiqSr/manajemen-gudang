<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JurnalUmumController extends Controller
{
    public function create()
    {
        $akuns = DB::table('akun')->orderBy('nama_akun')->get();
        return view('jurnal.create', ['akuns' => $akuns]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'entri' => 'required|array|min:2',
            'entri.*.id_akun' => 'required|integer|exists:akun,id',
            'entri.*.debit' => 'nullable|numeric|min:0',
            'entri.*.kredit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($request->entri as $item) {
            $totalDebit += $item['debit'] ?? 0;
            $totalKredit += $item['kredit'] ?? 0;
        }

        if ($totalDebit != $totalKredit || $totalDebit == 0) {
            return redirect()->back()->withInput()->with('error', 'Transaksi tidak seimbang! Total Debit harus sama dengan Total Kredit dan tidak boleh nol.');
        }

        try {
            DB::transaction(function () use ($request, $totalDebit) {
                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'keterangan' => $request->keterangan,
                    'referensi' => 'jurnal_umum',
                    'created_at' => now(),
                ]);

                foreach ($request->entri as $item) {
                    if (($item['debit'] ?? 0) > 0 || ($item['kredit'] ?? 0) > 0) {
                        DB::table('jurnal_detail')->insert([
                            'id_jurnal' => $jurnalId,
                            'id_akun' => $item['id_akun'],
                            'debit' => $item['debit'] ?? 0,
                            'kredit' => $item['kredit'] ?? 0,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }

        return redirect()->route('jurnal.create')->with('add_sukses', 'Jurnal Umum berhasil dicatat!');
    }
}
