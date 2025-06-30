<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $outlet_id_terpilih = $request->input('id_outlet');

        $penjualanQuery = DB::table('penjualan as p')
            ->join('outlets as o', 'p.id_outlet', '=', 'o.id')
            ->select('p.id', 'p.tanggal_penjualan', 'o.nama_outlet', 'p.nama_pelanggan', 'p.metode_pembayaran', 'p.status', 'p.total_pendapatan')
            ->whereBetween('p.tanggal_penjualan', [$tanggal_mulai, $tanggal_selesai])
            ->when($outlet_id_terpilih, function ($query, $outletId) {
                return $query->where('p.id_outlet', $outletId);
            })
            ->orderBy('p.tanggal_penjualan', 'desc')->orderBy('p.id', 'desc');

        $penjualans = $penjualanQuery->get();
        $penjualanIds = $penjualans->pluck('id')->toArray();

        $groupedDetails = DB::table('penjualan_detail as pd')
            ->join('bahan_baku as bb', 'pd.id_bahan_baku', '=', 'bb.id')
            ->select('pd.id_penjualan', 'bb.nama_bahan', 'pd.jumlah', 'pd.harga_saat_transaksi', 'pd.subtotal')
            ->whereIn('pd.id_penjualan', $penjualanIds)->get()->groupBy('id_penjualan');

        $outlets = DB::table('outlets')->orderBy('nama_outlet')->get();
        $bahanBaku = DB::table('bahan_baku')->orderBy('nama_bahan')->get();

        return view('penjualan.index', [
            'penjualans' => $penjualans,
            'groupedDetails' => $groupedDetails,
            'outlets' => $outlets,
            'bahanBaku' => $bahanBaku,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'outlet_id_terpilih' => $outlet_id_terpilih,
        ]);
    }

    public function store(Request $request)
    {

        // Daftar semua metode pembayaran yang valid
        $validPaymentMethods = [
            'Tunai',
            'Digital/Bank',
            'Kredit',
            'EDC BCA',
            'EDC BRI',
            'QRIS BRI',
            'EDC MANDIRI',
            'EDC BNI',
            'EDC BSI',
            'QRIS BTN',
            'EDC BTN',
            'QRIS-QPON',
            'DANA',
            'DANA DINEIN',
            'GOFOOD',
            'GRAB FOOD'
        ];

        $request->validate([
            'id_outlet' => 'required|integer|exists:outlets,id',
            'tanggal_penjualan' => 'required|date',
            'metode_pembayaran' => ['required', Rule::in($validPaymentMethods)],
            'nama_pelanggan' => 'required_if:metode_pembayaran,Kredit|nullable|string|max:100',
            'bahan' => 'required|array|min:1',
            'bahan.*.id' => 'required|integer|exists:bahan_baku,id',
            'bahan.*.jumlah' => 'required|integer|min:1',
            'bahan.*.harga' => 'required|numeric|min:0',
        ]);

        foreach ($request->bahan as $item) {
            $stokOutlet = DB::table('stok_outlet')->where('id_outlet', $request->id_outlet)->where('id_bahan_baku', $item['id'])->value('jumlah_stok');
            if ($stokOutlet < $item['jumlah']) {
                $namaBahan = DB::table('bahan_baku')->where('id', $item['id'])->value('nama_bahan');
                return redirect()->back()->withInput()->with('error', 'Stok ' . $namaBahan . ' di outlet tidak mencukupi. Stok tersedia: ' . ($stokOutlet ?? 0));
            }
        }

        try {
            DB::transaction(function () use ($request) {
                $totalPendapatan = 0;
                $totalHpp = 0;
                foreach ($request->bahan as $item) {
                    $totalPendapatan += $item['jumlah'] * $item['harga'];
                    $harga_pokok = DB::table('bahan_baku')->where('id', $item['id'])->value('harga_pokok');
                    $totalHpp += $item['jumlah'] * $harga_pokok;
                }

                $metode = $request->metode_pembayaran;
                $akunDebitPendapatan = 0;

                // Logika penentuan akun debit
                if ($metode == 'Tunai') {
                    $akunDebitPendapatan = 2; // Kas di Tangan
                } elseif ($metode == 'Kredit') {
                    $akunDebitPendapatan = 3; // Piutang Usaha
                } else {
                    // Semua metode digital lainnya diasumsikan masuk ke Bank
                    $akunDebitPendapatan = 1; // Kas di Bank
                }

                $status = ($metode == 'Kredit') ? 'Belum Lunas' : 'Lunas';

                $penjualanId = DB::table('penjualan')->insertGetId([
                    'id_outlet' => $request->id_outlet,
                    'nama_pelanggan' => $request->nama_pelanggan,
                    'tanggal_penjualan' => $request->tanggal_penjualan,
                    'total_pendapatan' => $totalPendapatan,
                    'metode_pembayaran' => $metode,
                    'status' => $status,
                    'created_at' => now(),
                ]);

                $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $request->tanggal_penjualan, 'keterangan' => 'Penjualan Produk di Outlet', 'referensi' => 'penjualan:' . $penjualanId, 'created_at' => now()]);
                DB::table('jurnal_detail')->insert([
                    ['id_jurnal' => $jurnalId, 'id_akun' => $akunDebitPendapatan, 'id_outlet' => $request->id_outlet, 'debit' => $totalPendapatan, 'kredit' => 0],
                    ['id_jurnal' => $jurnalId, 'id_akun' => 14, 'id_outlet' => $request->id_outlet, 'debit' => 0, 'kredit' => $totalPendapatan],
                    ['id_jurnal' => $jurnalId, 'id_akun' => 16, 'id_outlet' => $request->id_outlet, 'debit' => $totalHpp, 'kredit' => 0],
                    ['id_jurnal' => $jurnalId, 'id_akun' => 4, 'id_outlet' => $request->id_outlet, 'debit' => 0, 'kredit' => $totalHpp]
                ]);

                foreach ($request->bahan as $item) {
                    DB::table('penjualan_detail')->insert(['id_penjualan' => $penjualanId, 'id_bahan_baku' => $item['id'], 'jumlah' => $item['jumlah'], 'harga_saat_transaksi' => $item['harga'], 'subtotal' => $item['jumlah'] * $item['harga']]);
                    DB::table('stok_outlet')->where('id_outlet', $request->id_outlet)->where('id_bahan_baku', $item['id'])->decrement('jumlah_stok', $item['jumlah']);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
        }
        return redirect()->route('penjualan-bahan.index')->with('add_sukses', 'Transaksi penjualan berhasil dicatat!');
    }

    public function getStok(Request $request)
    {
        $id_outlet = $request->query('id_outlet');
        $id_bahan_baku = $request->query('id_bahan_baku');

        if (!$id_outlet || !$id_bahan_baku) {
            return response()->json(['stok' => 0]);
        }

        $stok = DB::table('stok_outlet')
            ->where('id_outlet', $id_outlet)
            ->where('id_bahan_baku', $id_bahan_baku)
            ->value('jumlah_stok');

        return response()->json(['stok' => $stok ?? 0]);
    }
}
