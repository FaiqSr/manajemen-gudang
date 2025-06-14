<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GudangController extends Controller
{
    public function stok()
    {
        $data = DB::table('stok_gudang_detail')
            ->join('stok_gudang', 'stok_gudang.id', '=', 'stok_gudang_detail.id_stok_gudang')
            ->join('bahan_baku', 'bahan_baku.id', '=', 'stok_gudang.id_bahan_baku')
            ->select('bahan_baku.nama_bahan', 'stok_gudang_detail.*')
            ->orderby('stok_gudang_detail.tanggal', 'desc')
            ->get();

        return view('gudang.stok', ['data' => $data]);
    }

    public function stokTerkini()
    {
        $data = DB::table('stok_gudang')
            ->join('bahan_baku', 'bahan_baku.id', '=', 'stok_gudang.id_bahan_baku')
            ->select('bahan_baku.nama_bahan', 'stok_gudang.*')
            ->get();

        return view('gudang.stok-terkini', compact('data'));
    }


    public function distribusi()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        $bahanBaku = DB::table('bahan_baku as bb')
            ->leftJoin('stok_gudang as sg', 'bb.id', '=', 'sg.id_bahan_baku')
            ->select('bb.id', 'bb.nama_bahan', 'bb.satuan', DB::raw('COALESCE(sg.jumlah_stok, 0) as stok_tersedia'))
            ->orderBy('bb.nama_bahan')
            ->get();

        return view('gudang.distribusi.index', [
            'outlets' => $outlets,
            'bahanBaku' => $bahanBaku
        ]);
    }

    public function distribute(Request $request)
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
                return redirect()->back()->withInput()->with('error', 'Stok ' . $namaBahan . ' tidak mencukupi. Stok tersedia: ' . ($stokGudang ?? 0));
            }
        }

        try {
            DB::transaction(function () use ($request) {
                $distribusiId = DB::table('distribusi')->insertGetId([
                    'id_outlet_tujuan' => $request->id_outlet_tujuan,
                    'tanggal_distribusi' => $request->tanggal_distribusi,
                    'created_at' => now(),
                ]);

                foreach ($request->bahan as $item) {
                    DB::table('distribusi_detail')->insert([
                        'id_distribusi' => $distribusiId,
                        'id_bahan_baku' => $item['id'],
                        'jumlah' => $item['jumlah'],
                    ]);

                    DB::table('stok_gudang')
                        ->where('id_bahan_baku', $item['id'])
                        ->decrement('jumlah_stok', $item['jumlah']);

                    $stokOutlet = DB::table('stok_outlet')
                        ->where('id_outlet', $request->id_outlet_tujuan)
                        ->where('id_bahan_baku', $item['id'])
                        ->first();

                    DB::table('stok_outlet')->updateOrInsert(
                        ['id_outlet' => $request->id_outlet_tujuan, 'id_bahan_baku' => $item['id']],
                        ['jumlah_stok' => ($stokOutlet->jumlah_stok ?? 0) + $item['jumlah'], 'last_updated' => now()]
                    );
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi distribusi: ' . $e->getMessage());
        }
        return redirect('gudang/distribusi')->with('add_sukses', 1);
    }
}
