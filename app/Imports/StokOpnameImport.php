<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StokOpnameImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $opnameData;

    public function __construct(array $opnameData)
    {
        $this->opnameData = $opnameData;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|exists:bahan_baku,nama_bahan',
            'stok_fisik' => 'required|numeric|min:0',
        ];
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $totalSelisihNilai = 0;
            $keteranganJurnal = 'Penyesuaian Stok Opname ' . DB::table('outlets')->where('id', $this->opnameData['id_outlet'])->value('nama_outlet') . ' via Import Tgl: ' . $this->opnameData['tanggal_opname'];
            $entriJurnalDetail = [];

            foreach ($rows as $row) {
                $bahan = DB::table('bahan_baku')->where('nama_bahan', $row['nama_bahan'])->first();
                $stokOutlet = DB::table('stok_outlet')->where('id_outlet', $this->opnameData['id_outlet'])->where('id_bahan_baku', $bahan->id)->first();
                
                $stokSistem = $stokOutlet->jumlah_stok ?? 0;
                $stokFisik = $row['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                if ($selisih != 0) {
                    DB::table('stok_outlet')->updateOrInsert(
                        ['id_outlet' => $this->opnameData['id_outlet'], 'id_bahan_baku' => $bahan->id],
                        ['jumlah_stok' => $stokFisik, 'last_updated' => now()]
                    );
                    
                    $nilaiSelisih = abs($selisih) * $bahan->harga_pokok;
                    $totalSelisihNilai += $nilaiSelisih;

                    if ($selisih < 0) {
                        $entriJurnalDetail[] = ['id_akun' => 4, 'debit' => 0, 'kredit' => $nilaiSelisih];
                    } else {
                        $entriJurnalDetail[] = ['id_akun' => 4, 'debit' => $nilaiSelisih, 'kredit' => 0];
                    }
                }
            }
            
            if ($totalSelisihNilai > 0) {
                $jurnalId = DB::table('jurnal')->insertGetId(['tanggal_transaksi' => $this->opnameData['tanggal_opname'], 'keterangan' => $keteranganJurnal, 'referensi' => 'stok_opname_import', 'created_at' => now()]);
                
                foreach($entriJurnalDetail as &$entri) {
                    $entri['id_jurnal'] = $jurnalId;
                    $entri['id_outlet'] = $this->opnameData['id_outlet'];
                }
                DB::table('jurnal_detail')->insert($entriJurnalDetail);
                
                DB::table('jurnal_detail')->insert(['id_jurnal' => $jurnalId, 'id_akun' => 22, 'id_outlet' => $this->opnameData['id_outlet'], 'debit' => $totalSelisihNilai, 'kredit' => 0]);
            }
        });
    }
}