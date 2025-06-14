<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendapatanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('outlet_id');

        $laporanPendapatan = DB::table('jurnal_detail as jd')
            ->selectRaw('a.nama_akun, SUM(jd.kredit - jd.debit) as total_pendapatan')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->where('a.kategori', '=', 'Pendapatan')
            ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('jd.id_outlet', $outletId);
            })
            ->groupBy('a.nama_akun')
            ->orderBy('a.nama_akun', 'asc')
            ->get();


        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('pendapatan.index', [
            'laporan'           => $laporanPendapatan,
            'outlets'           => $outlets,
            'outlet_id_terpilih' => $outlet_id_terpilih,
            'tanggal_mulai'     => $tanggal_mulai,
            'tanggal_selesai'   => $tanggal_selesai,
        ]);
    }
}
