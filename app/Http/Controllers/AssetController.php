<?php

namespace App\Http\Controllers;

use App\Exports\PenyusutanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function index()
    {
        $aset = DB::table('aset as a')
            ->leftJoin('outlets as o', 'a.id_outlet', '=', 'o.id')
            ->select('a.*', 'o.nama_outlet')
            ->orderBy('a.id_outlet')
            ->orderBy('a.nama_aset')
            ->get()
            ->groupBy('nama_outlet');

        return view('aset.index', ['asetGrouped' => $aset]);
    }

    public function create()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        $akunAset = DB::table('akun')->where('kategori', 'Aset')->where('nama_akun', 'NOT LIKE', '%Akumulasi%')->whereNotIn('id', [1, 2, 3, 4])->get();
        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('aset.create', [
            'outlets' => $outlets,
            'akunAset' => $akunAset,
            'akunKasBank' => $akunKasBank
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_aset' => 'required|string|max:100',
            'id_outlet' => 'nullable|integer',
            'jenis_aset' => 'nullable|string|max:50',
            'tanggal_perolehan' => 'required|date',
            'harga_perolehan' => 'required|numeric|min:0',
            'masa_manfaat_bulan' => 'required|integer|min:1',
            'nilai_residu' => 'required|numeric|min:0',
            'id_akun_aset' => 'required|integer|exists:akun,id',
            'id_akun_pembayaran' => 'required|integer|exists:akun,id',
        ]);

        $harga = $request->harga_perolehan;
        $residu = $request->nilai_residu;
        $masa = $request->masa_manfaat_bulan;

        if ($harga < $residu) {
            return redirect()->back()->withInput()->with('error', 'Harga perolehan tidak boleh lebih kecil dari nilai residu.');
        }

        $penyusutan_per_bulan = ($harga - $residu) / $masa;
        $kode_aset = 'ASET-' . time();

        try {
            DB::transaction(function () use ($request, $kode_aset, $penyusutan_per_bulan) {
                DB::table('aset')->insert([
                    'id_outlet' => $request->id_outlet,
                    'kode_aset' => $kode_aset,
                    'nama_aset' => $request->nama_aset,
                    'jenis_aset' => $request->jenis_aset,
                    'tanggal_perolehan' => $request->tanggal_perolehan,
                    'harga_perolehan' => $request->harga_perolehan,
                    'masa_manfaat_bulan' => $request->masa_manfaat_bulan,
                    'nilai_residu' => $request->nilai_residu,
                    'penyusutan_per_bulan' => $penyusutan_per_bulan,
                    'created_at' => now()
                ]);

                $keteranganJurnal = 'Pembelian Aset: ' . $request->nama_aset;
                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_perolehan,
                    'keterangan' => $keteranganJurnal,
                    'referensi' => $kode_aset
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->id_akun_aset,
                    'id_outlet' => $request->id_outlet,
                    'debit' => $request->harga_perolehan,
                    'kredit' => 0
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->id_akun_pembayaran,
                    'id_outlet' => null,
                    'debit' => 0,
                    'kredit' => $request->harga_perolehan
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan aset: ' . $e->getMessage());
        }

        return redirect()->route('aset.index')->with('add_sukses', 'Aset baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $aset = DB::table('aset')->where('id', $id)->first();
        if (!$aset) {
            return redirect()->route('aset.index')->with('error', 'Aset tidak ditemukan.');
        }

        DB::table('aset')->where('id', $id)->delete();

        return redirect()->route('aset.index')->with('delete_sukses', 'Aset berhasil dihapus.');
    }

    public function showPenyusutanAsset(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $exportType = $request->input('export');

        $tanggal_laporan = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $asets = DB::table('aset as a')
            ->leftJoin('outlets as o', 'a.id_outlet', '=', 'o.id')
            ->select('a.*', 'o.nama_outlet')
            ->orderBy('a.id_outlet')
            ->get();

        $reportData = [];

        foreach ($asets as $aset) {
            $tanggal_perolehan = Carbon::parse($aset->tanggal_perolehan);

            if ($tanggal_perolehan->gt($tanggal_laporan)) {
                continue;
            }

            $umur_aset_bulan = $tanggal_laporan->diffInMonths($tanggal_perolehan);

            if ($umur_aset_bulan >= $aset->masa_manfaat_bulan) {
                $bulan_penyusutan = $aset->masa_manfaat_bulan;
                $penyusutan_bulan_ini = 0;
            } else {
                $bulan_penyusutan = $umur_aset_bulan + 1;
                $penyusutan_bulan_ini = $aset->penyusutan_per_bulan;
            }

            $akumulasi_penyusutan = $aset->penyusutan_per_bulan * $bulan_penyusutan;
            if ($akumulasi_penyusutan > $aset->harga_perolehan - $aset->nilai_residu) {
                $akumulasi_penyusutan = $aset->harga_perolehan - $aset->nilai_residu;
            }

            $nilai_buku = $aset->harga_perolehan - $akumulasi_penyusutan;

            $aset->penyusutan_bulan_ini = $penyusutan_bulan_ini;
            $aset->akumulasi_penyusutan = $akumulasi_penyusutan;
            $aset->nilai_buku = $nilai_buku;

            $reportData[] = $aset;
        }

        $asetGrouped = collect($reportData)->groupBy('nama_outlet');
        $namaFile = 'laporan-penyusutan-' . $bulan . '-' . $tahun;

        if ($exportType == 'excel') {
            return Excel::download(new PenyusutanExport($asetGrouped, $bulan, $tahun), $namaFile . '.xlsx');
        }

        if ($exportType == 'pdf') {
            $pdf = Pdf::loadView('aset.penyusutan-export', ['asetGrouped' => $asetGrouped]);
            return $pdf->download($namaFile . '.pdf');
        }

        return view('aset.penyusutan', [
            'asetGrouped' => $asetGrouped,
            'bulan_terpilih' => $bulan,
            'tahun_terpilih' => $tahun
        ]);
    }
}
