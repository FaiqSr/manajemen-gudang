<?php

namespace App\Http\Controllers;

use App\Imports\StokOpnameImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StokOpnameController extends Controller
{
    public function create()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        return view('stok_opname.create', compact('outlets'));
    }

    public function getBahanByOutlet(Request $request)
    {
        $bahan = DB::table('stok_outlet as so')->join('bahan_baku as bb', 'so.id_bahan_baku', '=', 'bb.id')->where('so.id_outlet', $request->id_outlet)->select('bb.id', 'bb.nama_bahan', 'bb.satuan', 'so.jumlah_stok as stok_sistem')->orderBy('bb.nama_bahan')->get();
        return response()->json($bahan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|integer|exists:outlets,id',
            'tanggal_opname' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_bahan' => 'required|integer|exists:bahan_baku,id',
            'items.*.stok_fisik' => 'required|numeric|min:0',
            'items.*.stok_sistem' => 'required|numeric',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalSelisihNilai = 0;
                $keteranganJurnal = 'Penyesuaian Stok Opname ' . DB::table('outlets')->where('id', $request->id_outlet)->value('nama_outlet') . ' Tgl: ' . $request->tanggal_opname;
                $entriJurnalDetail = [];

                foreach ($request->items as $item) {
                    $stokFisik = $item['stok_fisik'];
                    $stokSistem = $item['stok_sistem'];
                    $selisih = $stokFisik - $stokSistem;

                    if ($selisih != 0) {
                        DB::table('stok_outlet')
                            ->where('id_outlet', $request->id_outlet)
                            ->where('id_bahan_baku', $item['id_bahan'])
                            ->update(['jumlah_stok' => $stokFisik, 'last_updated' => now()]);

                        $hargaPokok = DB::table('bahan_baku')->where('id', $item['id_bahan'])->value('harga_pokok');
                        $nilaiSelisih = abs($selisih) * $hargaPokok;
                        $totalSelisihNilai += $nilaiSelisih;

                        if ($selisih < 0) {
                            $entriJurnalDetail[] = ['id_akun' => 4, 'debit' => 0, 'kredit' => $nilaiSelisih];
                        } else {
                            $entriJurnalDetail[] = ['id_akun' => 4, 'debit' => $nilaiSelisih, 'kredit' => 0];
                        }
                    }
                }

                if ($totalSelisihNilai > 0) {
                    $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $request->tanggal_opname, 'keterangan' => $keteranganJurnal, 'referensi' => 'stok_opname', 'created_at' => now()]);

                    foreach ($entriJurnalDetail as &$entri) {
                        $entri['id_jurnal'] = $jurnalId;
                        $entri['id_outlet'] = $request->id_outlet;
                    }
                    DB::table('jurnal_detail')->insert($entriJurnalDetail);

                    DB::table('jurnal_detail')->insert(['id_jurnal' => $jurnalId, 'id_akun' => 22, 'id_outlet' => $request->id_outlet, 'debit' => $totalSelisihNilai, 'kredit' => 0]);
                }
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan stok opname: ' . $e->getMessage());
        }
        return redirect()->route('stok_opname.create')->with('add_sukses', 'Stok opname berhasil disimpan!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx',
            'id_outlet' => 'required|integer|exists:outlets,id',
            'tanggal_opname' => 'required|date',
        ]);
        
        try {
            $opnameData = $request->only(['id_outlet', 'tanggal_opname']);
            Excel::import(new StokOpnameImport($opnameData), $request->file('import_file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
             }
             return redirect()->back()->with('error', 'Gagal mengimpor data. Detail: ' . implode('; ', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }

        return redirect()->route('stok_opname.create')->with('add_sukses', 'Data stok opname berhasil diimpor!');
    }
}
