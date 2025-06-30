<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\DistribusiReportExport;
use App\Imports\DistribusiImport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DistribusiController extends Controller
{
    public function index()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        $bahanBaku = DB::table('bahan_baku as bb')->leftJoin('stok_gudang as sg', 'bb.id', '=', 'sg.id_bahan_baku')->select('bb.id', 'bb.nama_bahan', 'bb.satuan', DB::raw('COALESCE(sg.jumlah_stok, 0) as stok_tersedia'))->orderBy('bb.nama_bahan')->get();

        return view('gudang.distribusi.index', [
            'outlets' => $outlets,
            'bahanBaku' => $bahanBaku,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_outlet_tujuan' => 'required|integer|exists:outlets,id',
            'tanggal_distribusi' => 'required|date',
            'bahan' => 'required|array|min:1',
            'bahan.*.id' => 'required|integer|exists:bahan_baku,id',
            'bahan.*.jumlah' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($request->bahan as $item) {
            $stokGudang = DB::table('stok_gudang')->where('id_bahan_baku', $item['id'])->value('jumlah_stok');
            if ($stokGudang < $item['jumlah']) {
                $namaBahan = DB::table('bahan_baku')->where('id', $item['id'])->value('nama_bahan');
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Stok ' . $namaBahan . ' tidak mencukupi. Stok tersedia: ' . ($stokGudang ?? 0));
            }
        }

        try {
            DB::transaction(function () use ($request) {
                $distribusiId = DB::table('distribusi')->insertGetId(['id_outlet_tujuan' => $request->id_outlet_tujuan, 'tanggal_distribusi' => $request->tanggal_distribusi, 'created_at' => now()]);
                foreach ($request->bahan as $item) {
                    DB::table('distribusi_detail')->insert(['id_distribusi' => $distribusiId, 'id_bahan_baku' => $item['id'], 'jumlah' => $item['jumlah']]);
                    DB::table('stok_gudang')->where('id_bahan_baku', $item['id'])->decrement('jumlah_stok', $item['jumlah']);
                    $stokGudangId = DB::table('stok_gudang')->where('id_bahan_baku', $item['id'])->value('id');
                    DB::table('stok_gudang_detail')->insert(['id_stok_gudang' => $stokGudangId, 'status' => 'OUT', 'jumlah' => $item['jumlah'], 'tanggal' => $request->tanggal_distribusi]);
                    $stokOutlet = DB::table('stok_outlet')->where('id_outlet', $request->id_outlet_tujuan)->where('id_bahan_baku', $item['id'])->first();
                    DB::table('stok_outlet')->updateOrInsert(['id_outlet' => $request->id_outlet_tujuan, 'id_bahan_baku' => $item['id']], ['jumlah_stok' => ($stokOutlet->jumlah_stok ?? 0) + $item['jumlah'], 'last_updated' => now()]);
                }
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan distribusi: ' . $e->getMessage());
        }
        return redirect()->route('distribusi.index')->with('add_sukses', 'Distribusi bahan berhasil dicatat!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx',
            'id_outlet_tujuan' => 'required|integer|exists:outlets,id',
            'tanggal_distribusi' => 'required|date',
        ]);

        try {
            $distribusiData = $request->only(['id_outlet_tujuan', 'tanggal_distribusi']);
            Excel::import(new DistribusiImport($distribusiData), $request->file('import_file'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
        return redirect()->route('distribusi.index')->with('add_sukses', 'Data distribusi berhasil diimpor!');
    }

    public function laporan(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('id_outlet');
        $exportType = $request->input('export');

        $distribusiQuery = DB::table('distribusi as d')
            ->join('outlets as o', 'd.id_outlet_tujuan', '=', 'o.id')
            ->select('d.id', 'd.tanggal_distribusi', 'o.nama_outlet')
            ->whereBetween('d.tanggal_distribusi', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('d.id_outlet_tujuan', $outletId);
            })
            ->orderBy('d.tanggal_distribusi', 'desc')
            ->orderBy('d.id', 'desc');

        $distribusis = $distribusiQuery->get();
        $distribusiIds = $distribusis->pluck('id')->toArray();

        $groupedDetails = DB::table('distribusi_detail as dd')->join('bahan_baku as bb', 'dd.id_bahan_baku', '=', 'bb.id')->select('dd.id_distribusi', 'bb.nama_bahan', 'bb.satuan', 'dd.jumlah')->whereIn('dd.id_distribusi', $distribusiIds)->get()->groupBy('id_distribusi');

        if ($exportType) {
            $namaFile = 'laporan-distribusi-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data_export = [
                'distribusis' => $distribusis,
                'groupedDetails' => $groupedDetails,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];
            if ($exportType == 'excel') {
                return Excel::download(new DistribusiReportExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = FacadePdf::loadView('laporan.export.distribusi-export', $data_export)->setPaper('a4', 'portrait');
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        return view('laporan.distribusi', [
            'distribusis' => $distribusis,
            'groupedDetails' => $groupedDetails,
            'outlets' => $outlets,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'outlet_id_terpilih' => $outlet_id_terpilih,
        ]);
    }
}
