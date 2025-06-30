<?php

namespace App\Http\Controllers;

use App\Exports\PembelianExport;
use App\Imports\PembelianImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index()
    {
        $data = DB::table('suppliers')->get();

        return view('supplier.index', ['data' => $data]);
    }

    public function create()
    {
        return view('supplier.add');
    }

    public function add(Request $request)
    {
        DB::table('suppliers')->insert([
            'nama_supplier' => $request->namasupplier,
            'telepon' => $request->telpon,
            'alamat' => $request->alamat,
        ]);

        return redirect('supplier/index')->with('add_sukses', 1);
    }

    public function edit($id)
    {
        $row = DB::table('suppliers')->where('suppliers.id', $id)->first();

        return view('supplier.edit', [
            'row' => $row,
        ]);
    }

    public function update(Request $request)
    {
        DB::table('suppliers')
            ->where('id', $request->id)
            ->update([
                'namasupplier' => $request->namasupplier,
                'telpon' => $request->telpon,
                'alamat' => $request->alamat,
            ]);

        return redirect('supplier/index')->with('edit_sukses', 1);
    }

    public function delete($id)
    {
        DB::table('suppliers')->where('id', $id)->delete();

        return redirect()->back()->with('delete_sukses', 1);
    }

    public function pembelian(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
        $supplier_id_terpilih = $request->input('id_supplier');
        $exportType = $request->input('export');

        $subQuery = DB::table('pembelian_detail')->select('id_pembelian', DB::raw('SUM(jumlah) as total_qty'), DB::raw('COUNT(id) as jumlah_item'))->groupBy('id_pembelian');

        $pembelianQuery = DB::table('pembelian as p')
            ->join('suppliers as s', 'p.id_supplier', '=', 's.id')
            ->leftJoinSub($subQuery, 'pd', function ($join) {
                $join->on('p.id', '=', 'pd.id_pembelian');
            })
            ->select('p.id', 'p.tanggal_pembelian', 'p.nomor_invoice', 's.nama_supplier', 'p.total_biaya', 'p.status', 'p.metode_pembayaran', 'pd.total_qty', 'pd.jumlah_item')
            ->whereBetween('p.tanggal_pembelian', [$tanggal_mulai, $tanggal_selesai])
            ->when($supplier_id_terpilih, function ($query, $supplierId) {
                return $query->where('p.id_supplier', $supplierId);
            })
            ->orderBy('p.tanggal_pembelian', 'desc')
            ->orderBy('p.id', 'desc');

        $pembelians = $pembelianQuery->get();
        $pembelianIds = $pembelians->pluck('id')->toArray();

        $groupedDetails = DB::table('pembelian_detail as pd')->join('bahan_baku as bb', 'pd.id_bahan_baku', '=', 'bb.id')->select('pd.id_pembelian', 'bb.nama_bahan', 'bb.satuan', 'pd.jumlah', 'pd.subtotal', DB::raw('(CASE WHEN pd.jumlah > 0 THEN pd.subtotal / pd.jumlah ELSE 0 END) as harga_satuan'))->whereIn('pd.id_pembelian', $pembelianIds)->get()->groupBy('id_pembelian');

        if ($exportType) {
            $namaFile = 'riwayat-pembelian-' . $tanggal_mulai . '-sd-' . $tanggal_selesai;
            $data_export = [
                'pembelians' => $pembelians,
                'groupedDetails' => $groupedDetails,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
            ];
            if ($exportType == 'excel') {
                return Excel::download(new PembelianExport($data_export), $namaFile . '.xlsx');
            }
            if ($exportType == 'pdf') {
                $pdf = PDF::loadView('supplier.export.pembelian-export', $data_export);
                return $pdf->download($namaFile . '.pdf');
            }
        }

        $suppliers = DB::table('suppliers')->orderBy('nama_supplier')->get();
        $bahanBaku = DB::table('bahan_baku')->orderBy('nama_bahan')->get();

        return view('supplier.pembelian', [
            'pembelians' => $pembelians,
            'groupedDetails' => $groupedDetails,
            'suppliers' => $suppliers,
            'bahanBaku' => $bahanBaku,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'supplier_id_terpilih' => $supplier_id_terpilih,
        ]);
    }
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xls,xlsx',
            'id_supplier' => 'required|integer|exists:suppliers,id',
            'nomor_invoice' => 'nullable|string|max:50',
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'required|in:Kredit,Tunai,Digital/Bank',
            'id_akun_pembayaran' => 'required_if:metode_pembayaran,Tunai,Digital/Bank|nullable|integer',
            'tanggal_jatuh_tempo' => 'required_if:metode_pembayaran,Kredit|nullable|date',
        ]);

        try {
            $pembelianData = $request->only(['id_supplier', 'nomor_invoice', 'tanggal_pembelian', 'metode_pembayaran', 'id_akun_pembayaran', 'tanggal_jatuh_tempo']);

            Excel::import(new PembelianImport($pembelianData), $request->file('import_file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()
                ->back()
                ->with('error', 'Gagal mengimpor data. Detail: ' . implode('; ', $errorMessages));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }

        return redirect()->route('pembelian.create')->with('add_sukses', 'Data pembelian berhasil diimpor!');
    }

    public function add_pembelian(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_supplier' => 'required|integer|exists:suppliers,id',
            'nomor_invoice' => 'nullable|string|max:50',
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'required|in:Kredit,Tunai,Digital/Bank',
            'tanggal_jatuh_tempo' => 'required_if:metode_pembayaran,Kredit|nullable|date|after_or_equal:tanggal_pembelian',
            'id_akun_pembayaran' => 'required_if:metode_pembayaran,Tunai,Digital/Bank|nullable|integer',
            'bahan' => 'required|array|min:1',
            'bahan.*.id' => 'required|integer|exists:bahan_baku,id',
            'bahan.*.jumlah' => 'required|numeric|min:0.01',
            'bahan.*.subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('show_form', true);
        }

        try {
            DB::transaction(function () use ($request) {
                $total_biaya = 0;
                foreach ($request->bahan as $item) {
                    $total_biaya += $item['subtotal'];
                }

                $metode = $request->metode_pembayaran;
                $akunKredit = 0;
                if ($metode == 'Kredit') {
                    $akunKredit = 9;
                } else {
                    $akunKredit = $request->id_akun_pembayaran;
                }

                $status = $metode == 'Kredit' ? 'Belum Lunas' : 'Lunas';

                $pembelianId = DB::table('pembelian')->insertGetId([
                    'id_supplier' => $request->id_supplier,
                    'nomor_invoice' => $request->nomor_invoice,
                    'tanggal_pembelian' => $request->tanggal_pembelian,
                    'tanggal_jatuh_tempo' => $metode == 'Kredit' ? $request->tanggal_jatuh_tempo : null,
                    'total_biaya' => $total_biaya,
                    'metode_pembayaran' => $metode,
                    'status' => $status,
                    'created_at' => now(),
                ]);

                $keteranganJurnal = 'Pembelian Bahan Baku dari ' . DB::table('suppliers')->where('id', $request->id_supplier)->value('nama_supplier');
                if ($request->nomor_invoice) {
                    $keteranganJurnal .= ' Inv: ' . $request->nomor_invoice;
                }

                $jurnalId = DB::table('jurnal')->insertGetId([
                    'tanggal_transaksi' => $request->tanggal_pembelian,
                    'keterangan' => $keteranganJurnal,
                    'referensi' => 'pembelian:' . $pembelianId,
                    'created_at' => now(),
                ]);

                DB::table('jurnal_detail')->insert([['id_jurnal' => $jurnalId, 'id_akun' => 4, 'id_outlet' => null, 'debit' => $total_biaya, 'kredit' => 0], ['id_jurnal' => $jurnalId, 'id_akun' => $akunKredit, 'id_outlet' => null, 'debit' => 0, 'kredit' => $total_biaya]]);

                foreach ($request->bahan as $item) {
                    DB::table('pembelian_detail')->insert([
                        'id_pembelian' => $pembelianId,
                        'id_bahan_baku' => $item['id'],
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $stokGudang = DB::table('stok_gudang')->where('id_bahan_baku', $item['id'])->first();

                    DB::table('stok_gudang')->updateOrInsert(['id_bahan_baku' => $item['id']], ['jumlah_stok' => ($stokGudang->jumlah_stok ?? 0) + $item['jumlah'], 'last_updated' => now()]);

                    $stokGudangId = DB::table('stok_gudang')->where('id_bahan_baku', $item['id'])->value('id');

                    DB::table('stok_gudang_detail')->insert([
                        'id_stok_gudang' => $stokGudangId,
                        'status' => 'IN',
                        'jumlah' => $item['jumlah'],
                        'tanggal' => $request->tanggal_pembelian,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        return redirect()->back()->with('add_sukses', 'Pembelian berhasil dicatat!');
    }

    public function delete_pembelian($id)
    {
        DB::table('pembelian')->where('id', $id)->delete();
        DB::table('pembelian_detail')->where('id_pembelian', $id)->delete();

        return redirect()->back()->with('delete_sukses', 1);
    }

    public function bahanBaku()
    {
        $data = DB::table('bahan_baku')->get();

        return view('supplier.bahanbaku.index', ['data' => $data]);
    }

    public function addBahanBaku()
    {
        $satuan = DB::table('satuan')->get();
        return view('supplier.bahanbaku.add', compact('satuan'));
    }

    public function createBahanBaku(Request $req)
    {
        DB::table('bahan_baku')->insert([
            'nama_bahan' => $req->namabahan,
            'satuan' => $req->satuan,
        ]);

        return redirect('bahan')->with('add_sukses', 1);
    }

    public function editBahanBaku($id)
    {
        $bahanBaku = DB::table('bahan_baku')->where('id', $id)->first();
        $satuans = DB::table('satuan')->orderBy('nama_satuan')->get();

        if (!$bahanBaku) {
            abort(404);
        }

        return view('supplier.bahanbaku.edit', compact('bahanBaku', 'satuans'));
    }

    public function updateBahanBaku(Request $request, $id)
    {
        $request->validate([
            'nama_bahan' => ['required', 'string', 'max:100', \Illuminate\Validation\Rule::unique('bahan_baku')->ignore($id)],
            'satuan' => 'required|string|max:20',
            'harga_pokok' => 'required|numeric|min:0'
        ]);
    
        DB::table('bahan_baku')
            ->where('id', $id)
            ->update([
                'nama_bahan' => $request->nama_bahan,
                'satuan' => $request->satuan,
                'harga_pokok' => $request->harga_pokok,
                'updated_at' => now()
            ]);
    
        return redirect('bahan')->with('edit_sukses', 'Data bahan baku berhasil diperbarui.');
    }
}
