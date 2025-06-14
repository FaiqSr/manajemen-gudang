<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{
    public function index()
    {
        $outlet = DB::table('outlets')->get();

        return view('outlet.index', compact('outlet'));
    }

    public function add()
    {
        return view('outlet.add');
    }

    public function create(Request $req)
    {
        DB::table('outlets')->insert([
            'nama_outlet' => $req->nama,
            'telepon' => $req->telpon,
            'alamat' => $req->alamat,
            'pic' => $req->pic,
        ]);

        return redirect('outlet')->with('add_sukses', 1);
    }

    public function edit($id)
    {
        $data = DB::table('outlets')->where('id', $id)->first();

        return view('outlet.edit', compact('data'));
    }

    public function update(Request $req)
    {
        DB::table('outlets')->where('id', $req->id)->update([
            'nama_outlet' => $req->nama,
            'telepon' => $req->telpon,
            'alamat' => $req->alamat,
            'pic' => $req->pic
        ]);

        return redirect('outlet')->with('edit_sukses', 1);
    }

    public function delete($id)
    {
        DB::table('outlets')->where('id', $id)->delete();
        return redirect('outlet')->with('delete_sukses', 1);
    }

    public function stok($outlet_id)
    {
        $outlet = DB::table('outlets')->where('id', $outlet_id)->first();

        if (!$outlet) {
            return redirect()->route('outlet')->with('error', 'Outlet tidak ditemukan.');
        }

        $data = DB::table('stok_outlet')
            ->join('bahan_baku', 'stok_outlet.id_bahan_baku', '=', 'bahan_baku.id')
            ->where('stok_outlet.id_outlet', $outlet_id)
            ->select([
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan',
                'stok_outlet.jumlah_stok',
                'stok_outlet.id as stok_outlet_id', // Alias for clarity
                'stok_outlet.id_outlet'
            ])
            ->orderBy('bahan_baku.nama_bahan')
            ->get();

        return view('outlet.stok', compact('data', 'outlet'));
    }

    public function editStok($stok_outlet_id)
    {
        $stokItem = DB::table('stok_outlet')
            ->join('bahan_baku', 'stok_outlet.id_bahan_baku', '=', 'bahan_baku.id')
            ->join('outlets', 'stok_outlet.id_outlet', '=', 'outlets.id')
            ->where('stok_outlet.id', $stok_outlet_id)
            ->select(
                'stok_outlet.id',
                'stok_outlet.id_outlet',
                'stok_outlet.jumlah_stok',
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan',
                'outlets.nama_outlet'
            )
            ->first();

        if (!$stokItem) {
            return redirect()->back()->with('error', 'Data stok tidak ditemukan.');
        }

        return view('outlet.stok-edit', compact('stokItem'));
    }

    public function updateStok(Request $request)
    {
        $request->validate([
            'stok_id' => 'required|integer|exists:stok_outlet,id',
            'jumlah_stok' => 'required|numeric|min:0',
        ]);

        $stokOutlet = DB::table('stok_outlet')->where('id', $request->stok_id)->first();

        if (!$stokOutlet) {
            // This case should ideally not happen if exists validation works
            return redirect()->route('outlet')->with('error', 'Data stok tidak ditemukan.');
        }

        DB::table('stok_outlet')
            ->where('id', $request->stok_id)
            ->update([
                'jumlah_stok' => $request->jumlah_stok,
                'last_updated' => now(),
            ]);

        return redirect('outlet/stok/' . $stokOutlet->id_outlet)->with('edit_sukses', 'Stok berhasil diperbarui.');
    }

    // Distribusi

    public function distribusi()
    {
        $outlets = DB::table('outlets')->get();
        return view('outlet.distribusi.index', compact('outlets'));
    }

    public function getDistribusi(Request $request, $id)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());

        $outlet = DB::table('outlets')->where('id', $id)->first();

        if (!$outlet) {
            abort(404);
        }

        $distribusiData = DB::table('distribusi_detail')
            ->join('distribusi', 'distribusi_detail.id_distribusi', '=', 'distribusi.id')
            ->join('bahan_baku', 'distribusi_detail.id_bahan_baku', '=', 'bahan_baku.id')
            ->where('distribusi.id_outlet_tujuan', $id)
            ->whereBetween('distribusi.tanggal_distribusi', [$tanggal_mulai, $tanggal_selesai])
            ->select('distribusi_detail.id', 'bahan_baku.nama_bahan', 'bahan_baku.satuan', 'distribusi.tanggal_distribusi', 'distribusi_detail.jumlah')
            ->orderBy('distribusi.tanggal_distribusi', 'desc')
            ->get();

        $distribusiGrouped = $distribusiData->groupBy(function ($item) {
            return Carbon::parse($item->tanggal_distribusi)->format('F Y');
        });

        return view('outlet.distribusi.show', [
            'outlet' => $outlet,
            'distribusiGrouped' => $distribusiGrouped,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }

    // OUTLET OPERASIONAL
    public function operasional()
    {
        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();

        $akunBeban = DB::table('akun')
            ->where('kategori', 'Beban Operasional')
            ->orderBy('nama_akun')
            ->get();

        $akunKasBank = DB::table('akun')->whereIn('id', [1, 2])->get();

        return view('outlet.operasional.index', [
            'outlets' => $outlets,
            'akunBeban' => $akunBeban,
            'akunKasBank' => $akunKasBank
        ]);
    }

    public function storeOperasional(Request $request)
    {
        $request->validate([
            'id_outlet' => 'required|integer|exists:outlets,id',
            'tanggal_biaya' => 'required|date',
            'id_akun_beban' => 'required|integer|exists:akun,id',
            'id_akun_pembayaran' => 'required|integer|exists:akun,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_biaya,
                    'keterangan' => $request->keterangan,
                    'created_at' => now()
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->id_akun_beban,
                    'id_outlet' => $request->id_outlet,
                    'debit' => $request->jumlah,
                    'kredit' => 0
                ]);

                DB::table('jurnal_detail')->insert([
                    'id_jurnal' => $jurnalId,
                    'id_akun' => $request->id_akun_pembayaran,
                    'id_outlet' => $request->id_outlet,
                    'debit' => 0,
                    'kredit' => $request->jumlah
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect()->route('outlet/operasional')->with('add_sukses', 'Biaya operasional berhasil dicatat!');
    }
}
