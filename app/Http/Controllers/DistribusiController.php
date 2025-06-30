<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\DistribusiReportExport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class DistribusiController extends Controller
{
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
            ->orderBy('d.tanggal_distribusi', 'desc')->orderBy('d.id', 'desc');

        $distribusis = $distribusiQuery->get();
        $distribusiIds = $distribusis->pluck('id')->toArray();

        $groupedDetails = DB::table('distribusi_detail as dd')
            ->join('bahan_baku as bb', 'dd.id_bahan_baku', '=', 'bb.id')
            ->select('dd.id_distribusi', 'bb.nama_bahan', 'bb.satuan', 'dd.jumlah')
            ->whereIn('dd.id_distribusi', $distribusiIds)
            ->get()->groupBy('id_distribusi');

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
