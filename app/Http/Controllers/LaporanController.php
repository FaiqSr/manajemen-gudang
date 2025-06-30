<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\NeracaExport;
use App\Exports\ArusKasExport;
use App\Exports\LabaRugiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BukuBesarExport;
use App\Exports\LaporanStokExport;
use App\Exports\PembelianReportExport;
use App\Exports\RingkasanExport;
use App\Exports\StokOutletExport;
use Illuminate\Support\Facades\DB;
use App\Exports\StokPembelianExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenjualanReportExport;
use App\Exports\PendapatanReportExport;

class LaporanController extends Controller
{
    // public function showLaba(Request $request)
    // {
    //     $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
    //     $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
    //     $outlet_id_terpilih = $request->input('outlet_id');
    //     $exportType = $request->input('export');

    //     $baseQuery = DB::table('jurnal_detail as jd')
    //         ->join('akun as a', 'jd.id_akun', '=', 'a.id')
    //         ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id');

    //     $query = (clone $baseQuery)
    //         ->select(
    //             'a.kategori',
    //             'a.nama_akun',
    //             DB::raw('SUM(jd.debit) as total_debit'),
    //             DB::raw('SUM(jd.kredit) as total_kredit')
    //         )
    //         ->whereIn('a.kategori', [
    //             'Pendapatan',
    //             'Beban Pokok Penjualan',
    //             'Beban Operasional'
    //         ])
    //         ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
    //         ->when($outlet_id_terpilih, function ($query, $outletId) {
    //             return $query->where('jd.id_outlet', $outletId);
    //         })
    //         ->groupBy('a.kategori', 'a.nama_akun')
    //         ->orderBy('a.kategori')
    //         ->get();

    //     $laporan = [
    //         'Pendapatan' => [],
    //         'Beban Pokok Penjualan' => [],
    //         'Beban Operasional' => [],
    //         'totals' => ['pendapatan' => 0, 'hpp' => 0, 'operasional' => 0, 'laba_kotor' => 0, 'laba_bersih' => 0]
    //     ];

    //     foreach ($query as $item) {
    //         $total = ($item->kategori === 'Pendapatan') ? ($item->total_kredit - $item->total_debit) : ($item->total_debit - $item->total_kredit);
    //         $laporan[$item->kategori][$item->nama_akun] = $total;

    //         if ($item->kategori === 'Pendapatan') $laporan['totals']['pendapatan'] += $total;
    //         if ($item->kategori === 'Beban Pokok Penjualan') $laporan['totals']['hpp'] += $total;
    //         if ($item->kategori === 'Beban Operasional') $laporan['totals']['operasional'] += $total;
    //     }

    //     $laporan['totals']['laba_kotor'] = $laporan['totals']['pendapatan'] - $laporan['totals']['hpp'];
    //     $laporan['totals']['laba_bersih'] = $laporan['totals']['laba_kotor'] - $laporan['totals']['operasional'];

    //     $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : null;

    //     if ($exportType) {
    //         $namaFile = 'laporan-laba-rugi-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
    //         $data_export = [
    //             'laporan' => $laporan,
    //             'namaOutlet' => $namaOutlet,
    //             'tanggal_mulai' => $tanggal_mulai,
    //             'tanggal_selesai' => $tanggal_selesai
    //         ];

    //         if ($exportType == 'excel') {
    //             return Excel::download(new LabaRugiExport($data_export), $namaFile . '.xlsx');
    //         }
    //         if ($exportType == 'pdf') {
    //             $pdf = PDF::loadView('laporan.laba-rugi-export', $data_export);
    //             return $pdf->download($namaFile . '.pdf');
    //         }
    //     }

    //     $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

    //     return view('laporan.laba-rugi', [
    //         'laporan' => $laporan,
    //         'outlets' => $outlets,
    //         'outlet_id_terpilih' => $outlet_id_terpilih,
    //         'tanggal_mulai' => $tanggal_mulai,
    //         'tanggal_selesai' => $tanggal_selesai,
    //     ]);
    // }

    public function showLaba(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('outlet_id');
        $exportType = $request->input('export');

        $baseQuery = DB::table('jurnal_detail as jd')->join('akun as a', 'jd.id_akun', '=', 'a.id')->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id');

        $query = (clone $baseQuery)
            ->select('a.kategori', 'a.nama_akun', DB::raw('SUM(jd.debit) as total_debit'), DB::raw('SUM(jd.kredit) as total_kredit'))
            ->whereIn('a.kategori', ['Pendapatan', 'Beban Pokok Penjualan', 'Beban Operasional'])
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
            'totals' => ['pendapatan' => 0, 'hpp' => 0, 'operasional' => 0, 'laba_kotor' => 0, 'laba_bersih' => 0],
        ];

        foreach ($query as $item) {
            $total = $item->kategori === 'Pendapatan' ? $item->total_kredit - $item->total_debit : $item->total_debit - $item->total_kredit;
            $laporan[$item->kategori][$item->nama_akun] = $total;
            if ($item->kategori === 'Pendapatan') {
                $laporan['totals']['pendapatan'] += $total;
            }
            if ($item->kategori === 'Beban Pokok Penjualan') {
                $laporan['totals']['hpp'] += $total;
            }
            if ($item->kategori === 'Beban Operasional') {
                $laporan['totals']['operasional'] += $total;
            }
        }

        $tanggal_laporan_end = Carbon::parse($tanggal_selesai);
        $penyusutanDihitung = DB::table('aset')
            ->where('tanggal_perolehan', '<=', $tanggal_laporan_end->toDateString())
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('id_outlet', $outletId);
            })
            ->get()
            ->filter(function ($aset) use ($tanggal_laporan_end) {
                $tanggal_perolehan = Carbon::parse($aset->tanggal_perolehan);
                $akhir_manfaat = $tanggal_perolehan->copy()->addMonths($aset->masa_manfaat_bulan);
                return $tanggal_laporan_end->isBefore($akhir_manfaat);
            })
            ->sum('penyusutan_per_bulan');

        if ($penyusutanDihitung > 0) {
            $laporan['Beban Operasional']['Beban Penyusutan'] = ($laporan['Beban Operasional']['Beban Penyusutan'] ?? 0) + $penyusutanDihitung;
            $laporan['totals']['operasional'] += $penyusutanDihitung;
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
                'tanggal_selesai' => $tanggal_selesai,
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
            ->select('offsetting_akun.kategori', 'cash_entry.debit as kas_masuk', 'cash_entry.kredit as kas_keluar')
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
            ],
        ];

        foreach ($query as $trx) {
            $is_cash_in = $trx->kas_masuk > 0;
            $jumlah = $is_cash_in ? $trx->kas_masuk : $trx->kas_keluar;

            if (in_array($trx->kategori, ['Pendapatan', 'Beban Pokok Penjualan', 'Beban Operasional', 'Piutang Usaha', 'Utang Usaha'])) {
                if ($is_cash_in) {
                    $laporan['totals']['masuk_operasi'] += $jumlah;
                } else {
                    $laporan['totals']['keluar_operasi'] += $jumlah;
                }
            } elseif ($trx->kategori === 'Aset') {
                if ($is_cash_in) {
                    $laporan['totals']['masuk_investasi'] += $jumlah;
                } else {
                    $laporan['totals']['keluar_investasi'] += $jumlah;
                }
            } elseif (in_array($trx->kategori, ['Liabilitas', 'Ekuitas'])) {
                if ($is_cash_in) {
                    $laporan['totals']['masuk_pendanaan'] += $jumlah;
                } else {
                    $laporan['totals']['keluar_pendanaan'] += $jumlah;
                }
            }
        }

        $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : null;

        $data_export = [
            'laporan' => $laporan,
            'saldoAwal' => $saldoAwal,
            'namaOutlet' => $namaOutlet,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
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

        $totalPendapatan = (clone $baseQuery)->where('a.kategori', 'Pendapatan')->sum(DB::raw('jd.kredit - jd.debit'));

        $totalBiayaOperasional = (clone $baseQuery)->where('a.kategori', 'Beban Operasional')->sum(DB::raw('jd.debit - jd.kredit'));

        $rincianBiaya = (clone $baseQuery)->select('a.nama_akun', DB::raw('SUM(jd.debit - jd.kredit) as total'))->where('a.kategori', 'Beban Operasional')->groupBy('a.nama_akun')->orderBy('a.nama_akun')->get();

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

        $daftarBahan = DB::table('bahan_baku as bb')->leftJoin('stok_gudang as sg', 'bb.id', '=', 'sg.id_bahan_baku')->select('bb.id', 'bb.nama_bahan', 'bb.satuan', DB::raw('COALESCE(sg.jumlah_stok, 0) as jumlah_stok'))->orderBy('bb.nama_bahan')->get();

        $detailPembelian = DB::table('pembelian_detail as pd')
            ->join('pembelian as p', 'pd.id_pembelian', '=', 'p.id')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->select('pd.id_bahan_baku', 'p.tanggal_pembelian', 's.nama_supplier', 'pd.jumlah', 'pd.subtotal')
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
                'tanggal_selesai' => $tanggal_selesai,
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

    public function showPembelian(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $supplier_id_terpilih = $request->input('id_supplier');
        $exportType = $request->input('export');

        $subQuery = DB::table('pembelian_detail')
                      ->select('id_pembelian', DB::raw('SUM(jumlah) as total_qty'), DB::raw('COUNT(id) as jumlah_item'))
                      ->groupBy('id_pembelian');

        $pembelianQuery = DB::table('pembelian as p')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->leftJoinSub($subQuery, 'pd', function ($join) {
                $join->on('p.id', '=', 'pd.id_pembelian');
            })
            ->select('p.id', 'p.tanggal_pembelian', 'p.nomor_invoice', 's.nama_supplier', 'p.total_biaya', 'p.status', 'p.metode_pembayaran', 'pd.jumlah_item')
            ->whereBetween('p.tanggal_pembelian', [$tanggal_mulai, $tanggal_selesai])
            ->when($supplier_id_terpilih, function ($query, $supplierId) {
                return $query->where('p.id_supplier', $supplierId);
            })
            ->orderBy('p.tanggal_pembelian', 'desc')->orderBy('p.id', 'desc');

        $pembelians = $pembelianQuery->get();
        $pembelianIds = $pembelians->pluck('id')->toArray();

        $groupedDetails = DB::table('pembelian_detail as pd')
            ->join('bahan_baku as bb', 'pd.id_bahan_baku', '=', 'bb.id')
            ->select('pd.id_pembelian', 'bb.nama_bahan', 'bb.satuan', 'pd.jumlah', 'pd.subtotal', DB::raw('(CASE WHEN pd.jumlah > 0 THEN pd.subtotal / pd.jumlah ELSE 0 END) as harga_satuan'))
            ->whereIn('pd.id_pembelian', $pembelianIds)->get()->groupBy('id_pembelian');

        if ($exportType) {
            $namaFile = 'laporan-pembelian-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $namaSupplier = $supplier_id_terpilih ? DB::table('suppliers')->where('id', $supplier_id_terpilih)->value('nama_supplier') : 'Semua Supplier';
            $data_export = [
                'pembelians' => $pembelians, 'groupedDetails' => $groupedDetails,
                'namaSupplier' => $namaSupplier, 'tanggal_mulai' => $tanggal_mulai, 'tanggal_selesai' => $tanggal_selesai
            ];
            if ($exportType == 'excel') {
                return Excel::download(new PembelianReportExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.pembelian-export', $data_export)->setPaper('a4', 'landscape');
                return $pdf->download($namaFile . '.pdf');
            }
        }
        
        $suppliers = DB::table('suppliers')->orderBy('nama_supplier')->get();
        
        return view('laporan.pembelian', [
            'pembelians' => $pembelians, 'groupedDetails' => $groupedDetails,
            'suppliers' => $suppliers, 'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai, 'supplier_id_terpilih' => $supplier_id_terpilih,
        ]);
    }
    

    public function showNeraca(Request $request)
    {
        $per_tanggal = $request->input('per_tanggal', now()->toDateString());
        $tanggal_obj = Carbon::parse($per_tanggal);
        $awal_tahun = $tanggal_obj->copy()->startOfYear()->toDateString();
        $exportType = $request->input('export');

        $baseQuery = DB::table('jurnal_detail as jd')->join('akun as a', 'jd.id_akun', '=', 'a.id')->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id');

        $labaRugiTahunBerjalanQuery = (clone $baseQuery)->whereIn('a.kategori', ['Pendapatan', 'Beban Pokok Penjualan', 'Beban Operasional'])->whereBetween('j.tanggal_transaksi', [$awal_tahun, $per_tanggal]);

        $totalPendapatan = (clone $labaRugiTahunBerjalanQuery)->where('a.kategori', 'Pendapatan')->sum(DB::raw('jd.kredit - jd.debit'));

        $totalBeban = (clone $labaRugiTahunBerjalanQuery)->whereIn('a.kategori', ['Beban Pokok Penjualan', 'Beban Operasional'])->sum(DB::raw('jd.debit - jd.kredit'));

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

            if ($akun->kategori == 'Aset') {
                $totalAset += $saldoAkhir;
            }
            if ($akun->kategori == 'Liabilitas') {
                $totalLiabilitas += $saldoAkhir;
            }
            if ($akun->kategori == 'Ekuitas') {
                $totalEkuitas += $saldoAkhir;
            }
        }

        $laporan['Ekuitas']['Laba/Rugi Tahun Berjalan'] = $labaRugiTahunBerjalan;
        $totalEkuitas += $labaRugiTahunBerjalan;

        $laporan['totals'] = [
            'aset' => $totalAset,
            'liabilitas' => $totalLiabilitas,
            'ekuitas' => $totalEkuitas,
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

        $daftarAkun = DB::table('akun')->orderBy('id')->get();
        $akunTerpilih = null;
        $akunUntukLaporan = collect();

        if ($akun_terpilih_id) {
            $akunTerpilih = $daftarAkun->where('id', $akun_terpilih_id)->first();
            if ($akunTerpilih) {
                $akunUntukLaporan->push($akunTerpilih);
            }
        } else {
            $akunUntukLaporan = $daftarAkun;
        }
        $akunIds = $akunUntukLaporan->pluck('id')->toArray();

        $saldoAwal = DB::table('jurnal_detail as jd')->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')->select('jd.id_akun', DB::raw('SUM(jd.debit - jd.kredit) as saldo'))->whereIn('jd.id_akun', $akunIds)->where('j.tanggal_transaksi', '<', $tanggal_mulai)->groupBy('jd.id_akun')->get()->keyBy('id_akun');

        $transaksi = DB::table('jurnal_detail as jd')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->leftJoin('outlets as o', 'jd.id_outlet', '=', 'o.id')
            ->select('jd.id_akun', 'j.tanggal_transaksi', 'j.keterangan', 'j.referensi', 'jd.debit', 'jd.kredit', 'o.nama_outlet')
            ->whereIn('jd.id_akun', $akunIds)
            ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->orderBy('j.tanggal_transaksi', 'asc')
            ->orderBy('j.id', 'asc')
            ->get()
            ->groupBy('id_akun');

        if ($exportType) {
            $data_export = [
                'akunUntukLaporan' => $akunUntukLaporan,
                'transaksiGrouped' => $transaksi,
                'saldoAwalGrouped' => $saldoAwal,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];
            $namaFile = 'buku-besar-';
            $namaFile .= $akunTerpilih ? str_replace(' ', '-', strtolower($akunTerpilih->nama_akun)) : 'semua-akun';
            $namaFile .= '-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;

            if ($exportType == 'excel') {
                return Excel::download(new BukuBesarExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.buku-besar-export', $data_export)->setPaper('a4', 'landscape');
                return $pdf->download($namaFile . '.pdf');
            }
        }

        return view('laporan.buku-besar', [
            'daftarAkun' => $daftarAkun,
            'akunUntukLaporan' => $akunUntukLaporan,
            'akunTerpilih' => $akunTerpilih,
            'transaksiGrouped' => $transaksi,
            'saldoAwalGrouped' => $saldoAwal,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'akun_terpilih_id' => $akun_terpilih_id,
        ]);
    }

    public function showLaporanPenjualan(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('id_outlet');
        $exportType = $request->input('export');

        $penjualanQuery = DB::table('penjualan as p')
            ->join('outlets as o', 'p.id_outlet', '=', 'o.id')
            ->select('p.id', 'p.tanggal_penjualan', 'o.nama_outlet', 'p.nama_pelanggan', 'p.metode_pembayaran', 'p.status', 'p.total_pendapatan')
            ->whereBetween('p.tanggal_penjualan', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('p.id_outlet', $outletId);
            })
            ->orderBy('p.tanggal_penjualan', 'desc')
            ->orderBy('p.id', 'desc');

        $penjualans = $penjualanQuery->get();
        $penjualanIds = $penjualans->pluck('id')->toArray();

        $groupedDetails = DB::table('penjualan_detail as pd')->join('bahan_baku as bb', 'pd.id_bahan_baku', '=', 'bb.id')->select('pd.id_penjualan', 'bb.nama_bahan', 'pd.jumlah', 'pd.harga_saat_transaksi', 'pd.subtotal')->whereIn('pd.id_penjualan', $penjualanIds)->get()->groupBy('id_penjualan');

        if ($exportType) {
            $namaFile = 'laporan-penjualan-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : 'Semua Outlet';
            $data_export = [
                'penjualans' => $penjualans,
                'groupedDetails' => $groupedDetails,
                'namaOutlet' => $namaOutlet,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];

            if ($exportType == 'excel') {
                return Excel::download(new PenjualanReportExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.penjualan-export', $data_export)->setPaper('a4', 'landscape');
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.penjualan', [
            'penjualans' => $penjualans,
            'groupedDetails' => $groupedDetails,
            'outlets' => $outlets,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'outlet_id_terpilih' => $outlet_id_terpilih,
        ]);
    }

    public function showLaporanPendapatan(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('id_outlet');
        $exportType = $request->input('export');

        $pendapatanQuery = DB::table('jurnal_detail as jd')
            ->join('akun as a', 'jd.id_akun', '=', 'a.id')
            ->join('jurnal as j', 'jd.id_jurnal', '=', 'j.id')
            ->select('a.nama_akun', DB::raw('SUM(jd.kredit - jd.debit) as total_pendapatan'))
            ->where('a.kategori', '=', 'Pendapatan')
            ->whereBetween('j.tanggal_transaksi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('jd.id_outlet', $outletId);
            })
            ->groupBy('a.nama_akun')
            ->orderBy('a.nama_akun');

        $laporanPendapatan = $pendapatanQuery->get();

        if ($exportType) {
            $namaFile = 'laporan-pendapatan-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : 'Semua Outlet';
            $data_export = [
                'laporanPendapatan' => $laporanPendapatan,
                'namaOutlet' => $namaOutlet,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];

            if ($exportType == 'excel') {
                return Excel::download(new PendapatanReportExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.pendapatan-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.pendapatan', [
            'laporanPendapatan' => $laporanPendapatan,
            'outlets' => $outlets,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'outlet_id_terpilih' => $outlet_id_terpilih,
        ]);
    }

    public function showLaporanStok(Request $request)
    {
        $outlet_id_terpilih = $request->input('id_outlet');
        $exportType = $request->input('export');

        $stokGudang = DB::table('bahan_baku as bb')
            ->leftJoin('stok_gudang as sg', 'bb.id', '=', 'sg.id_bahan_baku')
            ->select('bb.nama_bahan', 'bb.satuan', DB::raw('COALESCE(sg.jumlah_stok, 0) as jumlah_stok'))
            ->orderBy('bb.nama_bahan')
            ->get();
        
        $stokOutletQuery = DB::table('stok_outlet as so')
            ->join('outlets as o', 'so.id_outlet', '=', 'o.id')
            ->join('bahan_baku as bb', 'so.id_bahan_baku', '=', 'bb.id')
            ->select('o.nama_outlet', 'bb.nama_bahan', 'bb.satuan', 'so.jumlah_stok')
            ->where('so.jumlah_stok', '>', 0)
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('so.id_outlet', $outletId);
            })
            ->orderBy('o.nama_outlet')
            ->orderBy('bb.nama_bahan');

        $stokOutlet = $stokOutletQuery->get()->groupBy('nama_outlet');

        if ($exportType) {
            $namaFile = 'laporan-stok-keseluruhan';
            $namaOutlet = $outlet_id_terpilih ? DB::table('outlets')->where('id', $outlet_id_terpilih)->value('nama_outlet') : 'Semua Outlet';
            $data_export = [
                'stokGudang' => $stokGudang,
                'stokOutlet' => $stokOutlet,
                'namaOutlet' => $namaOutlet
            ];

            if ($exportType == 'excel') {
                return Excel::download(new LaporanStokExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('laporan.export.stok-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.stok', [
            'stokGudang' => $stokGudang,
            'stokOutlet' => $stokOutlet,
            'outlets' => $outlets,
            'outlet_id_terpilih' => $outlet_id_terpilih,
        ]);
        
    }
}
