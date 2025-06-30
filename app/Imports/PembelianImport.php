<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PembelianImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $pembelianData;

    public function __construct(array $pembelianData)
    {
        $this->pembelianData = $pembelianData;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:0.01',
            'subtotal' => 'required|numeric|min:0',
        ];
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $total_biaya = $rows->sum('subtotal');
            
            $pembelianId = DB::table('pembelian')->insertGetId([
                'id_supplier' => $this->pembelianData['id_supplier'],
                'nomor_invoice' => $this->pembelianData['nomor_invoice'],
                'tanggal_pembelian' => $this->pembelianData['tanggal_pembelian'],
                'total_biaya' => $total_biaya,
                'metode_pembayaran' => $this->pembelianData['metode_pembayaran'],
                'status' => ($this->pembelianData['metode_pembayaran'] == 'Kredit') ? 'Belum Lunas' : 'Lunas',
                'tanggal_jatuh_tempo' => ($this->pembelianData['metode_pembayaran'] == 'Kredit') ? $this->pembelianData['tanggal_jatuh_tempo'] : null,
                'created_at' => now(),
            ]);

            $keteranganJurnal = 'Pembelian via Import dari ' . DB::table('suppliers')->where('id', $this->pembelianData['id_supplier'])->value('nama_supplier') . ' Inv: ' . $this->pembelianData['nomor_invoice'];
            $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $this->pembelianData['tanggal_pembelian'], 'keterangan' => $keteranganJurnal, 'referensi' => 'pembelian:' . $pembelianId, 'created_at' => now()]);

            $akunKredit = ($this->pembelianData['metode_pembayaran'] == 'Kredit') ? 9 : $this->pembelianData['id_akun_pembayaran'];
            DB::table('jurnal_detail')->insert([
                ['id_jurnal' => $jurnalId, 'id_akun' => 4, 'id_outlet' => null, 'debit' => $total_biaya, 'kredit' => 0],
                ['id_jurnal' => $jurnalId, 'id_akun' => $akunKredit, 'id_outlet' => null, 'debit' => 0, 'kredit' => $total_biaya]
            ]);

            foreach ($rows as $row) {
                
                $bahanBakuId = DB::table('bahan_baku')->where('nama_bahan', 'LIKE', $row['nama_bahan'])->value('id');
                
                if (!$bahanBakuId) {
                    $bahanBakuId = DB::table('bahan_baku')->insertGetId([
                        'nama_bahan' => $row['nama_bahan'],
                        'satuan' => 'pcs', 
                        'harga_pokok' => ($row['jumlah'] > 0) ? ($row['subtotal'] / $row['jumlah']) : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                DB::table('pembelian_detail')->insert(['id_pembelian' => $pembelianId, 'id_bahan_baku' => $bahanBakuId, 'jumlah' => $row['jumlah'], 'subtotal' => $row['subtotal']]);
                
                $stokGudang = DB::table('stok_gudang')->where('id_bahan_baku', $bahanBakuId)->first();
                DB::table('stok_gudang')->updateOrInsert(['id_bahan_baku' => $bahanBakuId], ['jumlah_stok' => ($stokGudang->jumlah_stok ?? 0) + $row['jumlah'], 'last_updated' => now()]);
                
                $stokGudangId = DB::table('stok_gudang')->where('id_bahan_baku', $bahanBakuId)->value('id');
                DB::table('stok_gudang_detail')->insert(['id_stok_gudang' => $stokGudangId, 'status' => 'IN', 'jumlah' => $row['jumlah'], 'tanggal' => $this->pembelianData['tanggal_pembelian']]);
            }
        });
    }
}