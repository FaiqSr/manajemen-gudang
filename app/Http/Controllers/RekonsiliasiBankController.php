<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekonsiliasiBankController extends Controller
{
    public function index(Request $request)
    {
        $id_akun_bank = $request->input('id_akun');
        $per_tanggal = $request->input('per_tanggal', now()->endOfMonth()->toDateString());
        $saldo_bank = $request->input('saldo_bank', 0);

        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->orderBy('nama_akun')->get();
        $akunTerpilih = null;
        $saldoBuku = 0;
        $transaksiBuku = collect();

        if ($id_akun_bank) {
            $akunTerpilih = DB::table('akun')->where('id', $id_akun_bank)->first();

            $saldoBuku = DB::table('jurnal_detail as jd')
                ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
                ->where('jd.id_akun', $id_akun_bank)
                ->where('j.tanggal_transaksi', '<=', $per_tanggal)
                ->sum(DB::raw('jd.debit - jd.kredit'));

            $tanggalAwalBulan = Carbon::parse($per_tanggal)->startOfMonth()->toDateString();

            $transaksiBuku = DB::table('jurnal_detail as jd')
                ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
                ->select('j.tanggal_transaksi', 'j.keterangan', 'jd.debit', 'jd.kredit')
                ->where('jd.id_akun', $id_akun_bank)
                ->whereBetween('j.tanggal_transaksi', [$tanggalAwalBulan, $per_tanggal])
                ->orderBy('j.tanggal_transaksi', 'asc')
                ->get();
        }

        return view('rekonsiliasi.index', [
            'akunKasBank' => $akunKasBank,
            'akunTerpilih' => $akunTerpilih,
            'saldoBuku' => $saldoBuku,
            'transaksiBuku' => $transaksiBuku,
            'per_tanggal_terpilih' => $per_tanggal,
            'id_akun_terpilih' => $id_akun_bank,
            'saldo_bank_terpilih' => $saldo_bank,
        ]);
    }
}
