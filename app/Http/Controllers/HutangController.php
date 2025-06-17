<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HutangController extends Controller
{
    public function index()
    {
        $hutangs = DB::table('pembelian as p')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->select('p.id', 'p.tanggal_pembelian', 'p.nomor_invoice', 's.nama_supplier', 'p.total_biaya')
            ->where('p.status', 'Belum Lunas')
            ->orderBy('p.tanggal_pembelian', 'asc')
            ->get();

        return view('hutang.index', ['hutangs' => $hutangs]);
    }

    public function create($id)
    {
        $pembelian = DB::table('pembelian as p')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->select('p.id', 'p.tanggal_pembelian', 'p.nomor_invoice', 's.nama_supplier', 'p.total_biaya', 'p.status')
            ->where('p.id', $id)
            ->first();

        if (!$pembelian || $pembelian->status == 'Lunas') {
            abort(404);
        }

        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('hutang.create', [
            'pembelian' => $pembelian,
            'akunKasBank' => $akunKasBank
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pembelian' => 'required|integer|exists:pembelian,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'id_akun_pembayaran' => 'required|integer|exists:akun,id',
        ]);

        $pembelian = DB::table('pembelian')->where('id', $request->id_pembelian)->first();
        if ($request->jumlah_bayar > $pembelian->total_biaya) {
            return redirect()->back()->withInput()->with('error', 'Jumlah bayar melebihi total tagihan.');
        }

        try {
            DB::transaction(function () use ($request, $pembelian) {
                $keteranganJurnal = 'Pembayaran Hutang ke ' . DB::table('suppliers')->where('id', $pembelian->id_supplier)->value('nama_supplier') . ' untuk Inv: ' . $pembelian->nomor_invoice;

                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_bayar,
                    'keterangan' => $keteranganJurnal,
                    'referensi' => 'pembayaran_hutang:' . $pembelian->id,
                    'created_at' => now()
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => 9,
                    'id_outlet' => null,
                    'debit' => $request->jumlah_bayar,
                    'kredit' => 0
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->id_akun_pembayaran,
                    'id_outlet' => null,
                    'debit' => 0,
                    'kredit' => $request->jumlah_bayar
                ]);

                DB::table('pembelian')->where('id', $request->id_pembelian)->update(['status' => 'Lunas']);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('hutang.index')->with('add_sukses', 'Pembayaran hutang berhasil dicatat!');
    }
}
