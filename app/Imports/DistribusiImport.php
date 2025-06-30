<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DistribusiImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $distribusiData;

    public function __construct(array $distribusiData)
    {
        $this->distribusiData = $distribusiData;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|exists:bahan_baku,nama_bahan',
            'jumlah' => 'required|numeric|min:1',
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $bahan = DB::table('bahan_baku')->where('nama_bahan', $row['nama_bahan'])->first();
            $stokGudang = DB::table('stok_gudang')->where('id_bahan_baku', $bahan->id)->value('jumlah_stok');

            if ($stokGudang < $row['jumlah']) {
                throw new \Exception('Stok ' . $row['nama_bahan'] . ' tidak mencukupi. Stok tersedia: ' . ($stokGudang ?? 0));
            }
        }

        DB::transaction(function () use ($rows) {
            $distribusiId = DB::table('distribusi')->insertGetId([
                'id_outlet_tujuan' => $this->distribusiData['id_outlet_tujuan'],
                'tanggal_distribusi' => $this->distribusiData['tanggal_distribusi'],
                'created_at' => now(),
            ]);

            foreach ($rows as $row) {
                $bahanBaku = DB::table('bahan_baku')->where('nama_bahan', $row['nama_bahan'])->first();
                
                DB::table('distribusi_detail')->insert([
                    'id_distribusi' => $distribusiId,
                    'id_bahan_baku' => $bahanBaku->id,
                    'jumlah' => $row['jumlah'],
                ]);

                DB::table('stok_gudang')->where('id_bahan_baku', $bahanBaku->id)->decrement('jumlah_stok', $row['jumlah']);
                
                $stokGudangId = DB::table('stok_gudang')->where('id_bahan_baku', $bahanBaku->id)->value('id');
                DB::table('stok_gudang_detail')->insert(['id_stok_gudang' => $stokGudangId, 'status' => 'OUT', 'jumlah' => $row['jumlah'], 'tanggal' => $this->distribusiData['tanggal_distribusi']]);

                $stokOutlet = DB::table('stok_outlet')->where('id_outlet', $this->distribusiData['id_outlet_tujuan'])->where('id_bahan_baku', $bahanBaku->id)->first();
                DB::table('stok_outlet')->updateOrInsert(
                    ['id_outlet' => $this->distribusiData['id_outlet_tujuan'], 'id_bahan_baku' => $bahanBaku->id],
                    ['jumlah_stok' => ($stokOutlet->jumlah_stok ?? 0) + $row['jumlah'], 'last_updated' => now()]
                );
            }
        });
    }
}