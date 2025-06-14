<?php

namespace App\Http\Controllers;

use App\Exports\ArusKasExport;
use App\Exports\BukuBesarExport;
use App\Exports\LabaRugiExport;
use App\Exports\NeracaExport;
use App\Exports\RingkasanExport;
use App\Exports\StokPembelianExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function showLaba(Request $request)
    {

        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('outlet_id');
        $exportType = $request->input('export');

        $baseQuery = DB::table('jurnal_detail as jd')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id');

        $query = (clone $baseQuery)
            ->select(
                'a.kategori',
                'a.nama_akun',
                DB::raw('SUM(jd.debit) as total_debit'),
                DB::raw('SUM(jd.kredit) as total_kredit')
            )
            ->whereIn('a.kategori', [
                'Pendapatan',
                'Beban Pokok Penjualan',
                'Beban Operasional'
            ])
            ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('jd.id_outlet', $outletId);
            })
            ->groupBy('a.kategori', 'a.nama_akun')
            ->orderBy('a.kategori')
            ->get();

        $laporan = [
            'Pendapatan' => [],
            'Beban Pokok Penjualan' => [],
            'Beban Operasional' => [],
            'totals' => ['pendapatan' => 0, 'hpp' => 0, 'operasional' => 0, 'laba_kotor' => 0, 'laba_bersih' => 0]
        ];

        foreach ($query as $item) {
            $total = ($item->kategori === 'Pendapatan') ? ($item->total_kredit - $item->total_debit) : ($item->total_debit - $item->total_kredit);
            $laporan[$item->kategori][$item->nama_akun] = $total;

            if ($item->kategori === 'Pendapatan') $laporan['totals']['pendapatan'] += $total;
            if ($item->kategori === 'Beban Pokok Penjualan') $laporan['totals']['hpp'] += $total;
            if ($item->kategori === 'Beban Operasional') $laporan['totals']['operasional'] += $total;
        }

        $laporan['totals']['laba_kotor'] = $laporan['totals']['pendapatan'] - $laporan['totals']['hpp'];
        $laporan['totals']['laba_bersih'] = $laporan['totals']['laba_kotor'] - $laporan['totals']['operasional'];

        $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : null;

        if ($exportType) {
            $namaFile = 'laporan-laba-rugi-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data_export = [
                'laporan' => $laporan,
                'namaOutlet' => $namaOutlet,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ];

            if ($exportType == 'excel') {
                return Excel::download(new LabaRugiExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.laba-rugi-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.laba-rugi', [
            'laporan' => $laporan,
            'outlets' => $outlets,
            'outlet_id_terpilih' => $outlet_id_terpilih,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }

    public function showArusKas(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('outlet_id');
        $exportType = $request->input('export');
        $akunKasBankIds = [1, 2];

        $saldoAwal = DB::table('jurnal_detail as jd')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->whereIn('jd.id_akun', $akunKasBankIds)
            ->where('j.tanggal_transaksi', '<', $tanggal_mulai)
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('jd.id_outlet', $outletId);
            })
            ->sum(DB::raw('jd.debit - jd.kredit'));

        $query = DB::table('jurnal_detail as cash_entry')
            ->join('jurnal', 'cash_entry.id_jurnal', '=', 'jurnal.id')
            ->join('jurnal_detail as offsetting_entry', 'cash_entry.id_jurnal', '=', 'offsetting_entry.id_jurnal')
            ->join('akun as offsetting_akun', 'offsetting_entry.id_akun', '=', 'offsetting_akun.id')
            ->select(
                'offsetting_akun.kategori',
                'cash_entry.debit as kas_masuk',
                'cash_entry.kredit as kas_keluar'
            )
            ->whereIn('cash_entry.id_akun', $akunKasBankIds)
            ->whereNotIn('offsetting_entry.id_akun', $akunKasBankIds)
            ->whereBetween('jurnal.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('cash_entry.id_outlet', $outletId);
            })
            ->get();

        $laporan = [
            'totals' => [
                'masuk_operasi' => 0,
                'keluar_operasi' => 0,
                'masuk_investasi' => 0,
                'keluar_investasi' => 0,
                'masuk_pendanaan' => 0,
                'keluar_pendanaan' => 0,
            ]
        ];

        foreach ($query as $trx) {
            $is_cash_in = $trx->kas_masuk > 0;
            $jumlah = $is_cash_in ? $trx->kas_masuk : $trx->kas_keluar;

            if (in_array($trx->kategori, ['Pendapatan', 'Beban Pokok Penjualan', 'Beban Operasional', 'Piutang Usaha', 'Utang Usaha'])) {
                if ($is_cash_in) $laporan['totals']['masuk_operasi'] += $jumlah;
                else $laporan['totals']['keluar_operasi'] += $jumlah;
            } elseif ($trx->kategori === 'Aset') {
                if ($is_cash_in) $laporan['totals']['masuk_investasi'] += $jumlah;
                else $laporan['totals']['keluar_investasi'] += $jumlah;
            } elseif (in_array($trx->kategori, ['Liabilitas', 'Ekuitas'])) {
                if ($is_cash_in) $laporan['totals']['masuk_pendanaan'] += $jumlah;
                else $laporan['totals']['keluar_pendanaan'] += $jumlah;
            }
        }

        $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : null;

        $data_export = [
            'laporan' => $laporan,
            'saldoAwal' => $saldoAwal,
            'namaOutlet' => $namaOutlet,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai
        ];

        if ($exportType == 'excel') {
            $namaFile = 'laporan-arus-kas-' . $tanggal_mulai . '-sd-' . $tanggal_selesai . '.xlsx';
            return Excel::download(new ArusKasExport($data_export), $namaFile);
        }

        if ($exportType == 'pdf') {
            $namaFile = 'laporan-arus-kas-' . $tanggal_mulai . '-sd-' . $tanggal_selesai . '.pdf';
            $pdf = PDF::loadView('laporan.export.arus-kas-export', $data_export);
            return $pdf->download($namaFile);
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.arus-kas', [
            'laporan' => $laporan,
            'saldoAwal' => $saldoAwal,
            'outlets' => $outlets,
            'outlet_id_terpilih' => $outlet_id_terpilih,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }

    public function showRingkasan(Request $request)
    {

        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('outlet_id');
        $exportType = $request->input('export');

        $baseQuery = DB::table('jurnal_detail as jd')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('jd.id_outlet', $outletId);
            });

        $totalPendapatan = (clone $baseQuery)
            ->where('a.kategori', 'Pendapatan')
            ->sum(DB::raw('jd.kredit - jd.debit'));

        $totalBiayaOperasional = (clone $baseQuery)
            ->where('a.kategori', 'Beban Operasional')
            ->sum(DB::raw('jd.debit - jd.kredit'));

        $rincianBiaya = (clone $baseQuery)
            ->select('a.nama_akun', DB::raw('SUM(jd.debit - jd.kredit) as total'))
            ->where('a.kategori', 'Beban Operasional')
            ->groupBy('a.nama_akun')
            ->orderBy('a.nama_akun')
            ->get();

        $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : null;

        if ($exportType) {
            $namaFile = 'ringkasan-pendapatan-vs-biaya-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data_export = [
                'totalPendapatan' => $totalPendapatan,
                'totalBiayaOperasional' => $totalBiayaOperasional,
                'rincianBiaya' => $rincianBiaya,
                'namaOutlet' => $namaOutlet,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];

            if ($exportType == 'excel') {
                return Excel::download(new RingkasanExport($data_export), $namaFile . '.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.ringkasan-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.ringkasan', [
            'totalPendapatan' => $totalPendapatan,
            'totalBiayaOperasional' => $totalBiayaOperasional,
            'rincianBiaya' => $rincianBiaya,
            'outlets' => $outlets,
            'outlet_id_terpilih' => $outlet_id_terpilih,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }

    public function showStokDanPembelian(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $exportType = $request->input('export');

        $daftarBahan = DB::table('bahan_baku as bb')
            ->leftJoin('stok_gudang as sg', 'bb.id', '=', 'sg.id_bahan_baku')
            ->select('bb.id', 'bb.nama_bahan', 'bb.satuan', DB::raw('COALESCE(sg.jumlah_stok, 0) as jumlah_stok'))
            ->orderBy('bb.nama_bahan')
            ->get();

        $detailPembelian = DB::table('pembelian_detail as pd')
            ->join('pembelian as p', 'pd.id_pembelian', '=', 'p.id')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->select(
                'pd.id_bahan_baku',
                'p.tanggal_pembelian',
                's.nama_supplier',
                'pd.jumlah',
                'pd.subtotal'
            )
            ->whereBetween('p.tanggal_pembelian', [$tanggal_mulai, $tanggal_selesai])
            ->orderBy('p.tanggal_pembelian', 'desc')
            ->get()
            ->groupBy('id_bahan_baku');

        if ($exportType) {
            $namaFile = 'laporan-stok-pembelian-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data = [
                'daftarBahan' => $daftarBahan,
                'detailPembelian' => $detailPembelian,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ];

            if ($exportType == 'excel') {
                return Excel::download(new StokPembelianExport($daftarBahan, $detailPembelian, $tanggal_mulai, $tanggal_selesai), $namaFile . '.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.stok-pembelian-export', $data)->setPaper('a4', 'portrait');
                return $pdf->download($namaFile . '.pdf');
            }
        }

        return view('laporan.stok-pembelian', [
            'daftarBahan' => $daftarBahan,
            'detailPembelian' => $detailPembelian,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }

    public function showNeraca(Request $request)
    {
        $per_tanggal = $request->input('per_tanggal', now()->toDateString());
        $tanggal_obj = Carbon::parse($per_tanggal);
        $awal_tahun = $tanggal_obj->copy()->startOfYear()->toDateString();
        $exportType = $request->input('export');

        $baseQuery = DB::table('jurnal_detail as jd')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id');

        $labaRugiTahunBerjalanQuery = (clone $baseQuery)
            ->whereIn('a.kategori', ['Pendapatan', 'Beban Pokok Penjualan', 'Beban Operasional'])
            ->whereBetween('j.tanggal_transaksi', [$awal_tahun, $per_tanggal]);

        $totalPendapatan = (clone $labaRugiTahunBerjalanQuery)
            ->where('a.kategori', 'Pendapatan')
            ->sum(DB::raw('jd.kredit - jd.debit'));

        $totalBeban = (clone $labaRugiTahunBerjalanQuery)
            ->whereIn('a.kategori', ['Beban Pokok Penjualan', 'Beban Operasional'])
            ->sum(DB::raw('jd.debit - jd.kredit'));

        $labaRugiTahunBerjalan = $totalPendapatan - $totalBeban;

        $saldoAkun = DB::table('jurnal_detail as jd')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->select('a.nama_akun', 'a.kategori', 'a.saldo_normal', DB::raw('SUM(jd.debit - jd.kredit) as saldo'))
            ->whereIn('a.kategori', ['Aset', 'Liabilitas', 'Ekuitas'])
            ->where('j.tanggal_transaksi', '<=', $per_tanggal)
            ->groupBy('a.nama_akun', 'a.kategori', 'a.saldo_normal')
            ->havingRaw('SUM(jd.debit - jd.kredit) != 0')
            ->get();

        $laporan = [
            'Aset' => [],
            'Liabilitas' => [],
            'Ekuitas' => [],
        ];

        $totalAset = 0;
        $totalLiabilitas = 0;
        $totalEkuitas = 0;

        foreach ($saldoAkun as $akun) {
            $saldoAkhir = $akun->saldo;
            if ($akun->saldo_normal == 'Kredit') {
                $saldoAkhir *= -1;
            }
            $laporan[$akun->kategori][$akun->nama_akun] = $saldoAkhir;

            if ($akun->kategori == 'Aset') $totalAset += $saldoAkhir;
            if ($akun->kategori == 'Liabilitas') $totalLiabilitas += $saldoAkhir;
            if ($akun->kategori == 'Ekuitas') $totalEkuitas += $saldoAkhir;
        }

        $laporan['Ekuitas']['Laba/Rugi Tahun Berjalan'] = $labaRugiTahunBerjalan;
        $totalEkuitas += $labaRugiTahunBerjalan;

        $laporan['totals'] = [
            'aset' => $totalAset,
            'liabilitas' => $totalLiabilitas,
            'ekuitas' => $totalEkuitas
        ];

        $namaFile = 'laporan-neraca-per-' . $per_tanggal;

        if ($exportType == 'excel') {
            return Excel::download(new NeracaExport($laporan, $per_tanggal), $namaFile . '.xlsx');
        }

        if ($exportType == 'pdf') {
            $pdf = PDF::loadView('laporan.export.neraca-export', ['laporan' => $laporan, 'per_tanggal' => $per_tanggal]);
            return $pdf->download($namaFile . '.pdf');
        }

        return view('laporan.neraca', [
            'laporan' => $laporan,
            'per_tanggal' => $per_tanggal,
        ]);
    }

    public function showBukuBesar(Request $request)
    {

        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $akun_terpilih_id = $request->input('id_akun');
        $exportType = $request->input('export');

        $daftarAkun = DB::table('akun')->orderBy('nama_akun')->get();
        $transaksi = collect();
        $saldoAwal = 0;
        $akunTerpilih = null;

        if ($akun_terpilih_id) {
            $akunTerpilih = DB::table('akun')->where('id', $akun_terpilih_id)->first();

            $saldoAwal = DB::table('jurnal_detail as jd')
                ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
                ->where('jd.id_akun', $akun_terpilih_id)
                ->where('j.tanggal_transaksi', '<', $tanggal_mulai)
                ->sum(DB::raw('jd.debit - jd.kredit'));

            if ($akunTerpilih && $akunTerpilih->saldo_normal == 'Kredit') {
                $saldoAwal *= -1;
            }

            $transaksi = DB::table('jurnal_detail as jd')
                ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
                ->leftJoin('outlets as o', 'jd.id_outlet', '=', 'o.id')
                ->select(
                    'j.tanggal_transaksi',
                    'j.keterangan',
                    'j.referensi',
                    'jd.debit',
                    'jd.kredit',
                    'o.nama_outlet'
                )
                ->where('jd.id_akun', $akun_terpilih_id)
                ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
                ->orderBy('j.tanggal_transaksi', 'asc')
                ->orderBy('j.id', 'asc')
                ->get();

            $data_export = [
                'akunTerpilih' => $akunTerpilih,
                'transaksi' => $transaksi,
                'saldoAwal' => $saldoAwal,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ];

            $namaFile = 'buku-besar-' . str_replace(' ', '-', strtolower($akunTerpilih->nama_akun)) . '-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;

            if ($exportType == 'excel') {
                return Excel::download(new BukuBesarExport($data_export), $namaFile . '.xlsx');
            }

            if ($exportType == 'pdf') {
                $pdf = Pdf::loadView('laporan.export.buku-besar-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        return view('laporan.buku-besar', [
            'daftarAkun' => $daftarAkun,
            'akunTerpilih' => $akunTerpilih,
            'transaksi' => $transaksi,
            'saldoAwal' => $saldoAwal,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'akun_terpilih_id' => $akun_terpilih_id
        ]);
    }
}
