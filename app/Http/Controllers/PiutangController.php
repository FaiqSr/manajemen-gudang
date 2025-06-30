<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index()
    {
        $piutangs = DB::table('penjualan as p')
            ->join('outlets as o', 'p.id_outlet', '=', 'o.id')
            ->select('p.id', 'p.tanggal_penjualan', 'p.tanggal_jatuh_tempo', 'p.nama_pelanggan', 'o.nama_outlet', 'p.sisa_piutang', 'p.status') // Pastikan tanggal_jatuh_tempo & status ada di sini
            ->where('p.status', 'Belum Lunas')
            ->where('p.sisa_piutang', '>', 0)
            ->orderBy('p.tanggal_jatuh_tempo', 'asc')
            ->get();

        return view('piutang.index', ['piutangs' => $piutangs]);
    }

    public function create($id)
    {
        $penjualan = DB::table('penjualan as p')
            ->join('outlets as o', 'p.id_outlet', '=', 'o.id')
            ->select('p.id', 'p.tanggal_penjualan', 'p.nama_pelanggan', 'o.nama_outlet', 'p.total_pendapatan', 'p.sisa_piutang', 'p.status')
            ->where('p.id', $id)->first();

        if (!$penjualan || $penjualan->status == 'Lunas') {
            abort(404);
        }
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();
        return view('piutang.create', ['penjualan' => $penjualan, 'akunKasBank' => $akunKasBank]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penjualan' => 'required|integer|exists:penjualan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'id_akun_penerimaan' => 'required|integer|exists:akun,id',
        ]);

        $penjualan = DB::table('penjualan')->where('id', $request->id_penjualan)->first();
        if ($request->jumlah_bayar > $penjualan->sisa_piutang) {
            return redirect()->back()->withInput()->with('error', 'Jumlah bayar melebihi sisa tagihan.');
        }

        try {
            DB::transaction(function () use ($request, $penjualan) {
                $keteranganJurnal = 'Penerimaan Piutang dari ' . $penjualan->nama_pelanggan;
                $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $request->tanggal_bayar, 'keterangan' => $keteranganJurnal, 'referensi' => 'pelunasan_piutang:' . $penjualan->id, 'created_at' => now()]);
                DB::table('jurnal_detail')->insert(['id_jurnal' => $jurnalId, 'id_akun' => $request->id_akun_penerimaan, 'id_outlet' => null, 'debit' => $request->jumlah_bayar, 'kredit' => 0]);
                DB::table('jurnal_detail')->insert(['id_jurnal' => $jurnalId, 'id_akun' => 3, 'id_outlet' => $penjualan->id_outlet, 'debit' => 0, 'kredit' => $request->jumlah_bayar]);

                $sisaPiutangBaru = $penjualan->sisa_piutang - $request->jumlah_bayar;
                $statusBaru = ($sisaPiutangBaru <= 0) ? 'Lunas' : 'Belum Lunas';

                DB::table('penjualan')->where('id', $request->id_penjualan)->update([
                    'sisa_piutang' => $sisaPiutangBaru,
                    'status' => $statusBaru
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('piutang.index')->with('add_sukses', 'Penerimaan piutang berhasil dicatat!');
    }
    public function laporan(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfYear()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->toDateString());
        $exportType = $request->input('export');

        $subQueryPenerimaan = DB::table('jurnal as j')
            ->join('jurnal_detail as jd', 'j.id', '=', 'jd.id_jurnal')
            ->select(
                DB::raw("CAST(SUBSTRING_INDEX(j.referensi, ':', -1) AS UNSIGNED) as id_penjualan"),
                DB::raw('SUM(jd.kredit) as total_diterima')
            )
            ->where('j.referensi', 'LIKE', 'pelunasan_piutang:%')
            ->where('jd.id_akun', 3)
            ->groupBy('id_penjualan');

        $piutangQuery = DB::table('penjualan as p')
            ->leftJoinSub($subQueryPenerimaan, 'pp', function ($join) {
                $join->on('p.id', '=', 'pp.id_penjualan');
            })
            ->select(
                'p.id',
                'p.nama_pelanggan',
                'p.tanggal_penjualan',
                'p.total_pendapatan',
                DB::raw('COALESCE(pp.total_diterima, 0) as total_diterima'),
                DB::raw('p.total_pendapatan - COALESCE(pp.total_diterima, 0) as sisa_piutang')
            )
            ->where('p.metode_pembayaran', 'Kredit')
            ->whereBetween('p.tanggal_penjualan', [$tanggal_mulai, $tanggal_selesai]);

        $dataPiutang = $piutangQuery->orderBy('p.tanggal_penjualan', 'asc')
            ->get()
            ->where('sisa_piutang', '>', 0);

        if ($exportType) {
            $namaFile = 'laporan-piutang-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data = ['dataPiutang' => $dataPiutang, 'tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai];

            if ($exportType == 'excel') {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanPiutangExport($data), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.export.piutang-export', $data)->setPaper('a4', 'landscape');
                return $pdf->download($namaFile . '.pdf');
            }
        }

        return view('laporan.piutang', compact('dataPiutang', 'tanggal_mulai', 'tanggal_selesai'));
    }
}
