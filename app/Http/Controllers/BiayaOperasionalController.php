<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiayaOperasionalController extends Controller
{
    public function index()
    {
        $biayaBelumLunas = DB::table('biaya_operasional as bo')
            ->join('outlets as o', 'bo.id_outlet', '=', 'o.id')
            ->join('akun as a', 'bo.id_akun_beban', '=', 'a.id')
            ->select('bo.id', 'bo.tanggal_tagihan', 'bo.keterangan', 'o.nama_outlet', 'a.nama_akun as jenis_biaya', 'bo.jumlah')
            ->where('bo.status', 'Belum Lunas')
            ->orderBy('bo.tanggal_tagihan', 'asc')
            ->get();

        return view('biaya.index', ['biayaBelumLunas' => $biayaBelumLunas]);
    }

    public function create()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        $akunBeban = DB::table('akun')->where('kategori', 'Beban Operasional')->orderBy('nama_akun')->get();
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('biaya.create', compact('outlets', 'akunBeban', 'akunKasBank'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|integer|exists:outlets,id',
            'id_akun_beban' => 'required|integer|exists:akun,id',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_biaya' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:Kredit,Tunai,Digital/Bank',
            'id_akun_pembayaran' => 'required_if:metode_pembayaran,Tunai,Digital/Bank|nullable|integer|exists:akun,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $metode = $request->metode_pembayaran;
                $status = ($metode == 'Kredit') ? 'Belum Lunas' : 'Lunas';

                $akunKredit = 0;
                if ($metode == 'Kredit') $akunKredit = 24;
                else $akunKredit = $request->id_akun_pembayaran;

                $biayaId = DB::table('biaya_operasional')->insertGetId([
                    'id_outlet' => $request->id_outlet,
                    'id_akun_beban' => $request->id_akun_beban,
                    'keterangan' => $request->keterangan,
                    'jumlah' => $request->jumlah,
                    'tanggal_tagihan' => $request->tanggal_biaya,
                    'status' => $status
                ]);

                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_biaya,
                    'keterangan' => 'Biaya: ' . $request->keterangan,
                    'referensi' => 'biaya:' . $biayaId
                ]);

                DB::table('jurnal_detail')->insert([
                    ['id_jurnal' => $jurnalId, 'id_akun' => $request->id_akun_beban, 'id_outlet' => $request->id_outlet, 'debit' => $request->jumlah, 'kredit' => 0],
                    ['id_jurnal' => $jurnalId, 'id_akun' => $akunKredit, 'id_outlet' => $request->id_outlet, 'debit' => 0, 'kredit' => $request->jumlah]
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat biaya: ' . $e->getMessage());
        }

        $pesan = ($request->metode_pembayaran == 'Kredit') ? 'Biaya berhasil dicatat sebagai hutang.' : 'Biaya berhasil dicatat dan dibayar.';
        return redirect()->route('biaya.create')->with('add_sukses', $pesan);
    }

    public function paymentCreate($id)
    {
        $biaya = DB::table('biaya_operasional as bo')
            ->join('outlets as o', 'bo.id_outlet', '=', 'o.id')
            ->select('bo.*', 'o.nama_outlet')
            ->where('bo.id', $id)->first();
        if (!$biaya || $biaya->status == 'Lunas') {
            abort(404);
        }
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();
        return view('biaya.bayar', compact('biaya', 'akunKasBank'));
    }

    public function paymentStore(Request $request)
    {
        $request->validate(['id_biaya' => 'required|integer|exists:biaya_operasional,id', 'tanggal_bayar' => 'required|date', 'id_akun_pembayaran' => 'required|integer|exists:akun,id']);
        $biaya = DB::table('biaya_operasional')->where('id', $request->id_biaya)->first();

        try {
            DB::transaction(function () use ($request, $biaya) {
                $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $request->tanggal_bayar, 'keterangan' => 'Pembayaran Biaya: ' . $biaya->keterangan, 'referensi' => 'pembayaran_biaya:' . $biaya->id]);
                DB::table('jurnal_detail')->insert([
                    ['id_jurnal' => $jurnalId, 'id_akun' => 24, 'id_outlet' => $biaya->id_outlet, 'debit' => $biaya->jumlah, 'kredit' => 0],
                    ['id_jurnal' => $jurnalId, 'id_akun' => $request->id_akun_pembayaran, 'id_outlet' => $biaya->id_outlet, 'debit' => 0, 'kredit' => $biaya->jumlah]
                ]);
                DB::table('biaya_operasional')->where('id', $request->id_biaya)->update(['status' => 'Lunas']);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
        return redirect()->route('biaya.index')->with('add_sukses', 'Pembayaran biaya berhasil dicatat.');
    }
}
