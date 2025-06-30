<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StokPembelianExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $daftarBahan;
    protected $detailPembelian;
    protected $tanggal_mulai;
    protected $tanggal_selesai;

    public function __construct($daftarBahan, $detailPembelian, $tanggal_mulai, $tanggal_selesai)
    {
        $this->daftarBahan = $daftarBahan;
        $this->detailPembelian = $detailPembelian;
        $this->tanggal_mulai = $tanggal_mulai;
        $this->tanggal_selesai = $tanggal_selesai;
    }

    public function headings(): array
    {
        return [
            'Nama Bahan',
            'Stok Gudang Saat Ini',
            'Satuan',
            'Tanggal Pembelian',
            'Supplier',
            'Jumlah Dibeli',
            'Subtotal Pembelian (Rp)'
        ];
    }

    public function array(): array
    {
        $exportData = [];

        foreach ($this->daftarBahan as $bahan) {
            $isFirstRow = true;

            if (isset($this->detailPembelian[$bahan->id]) && count($this->detailPembelian[$bahan->id]) > 0) {
                foreach ($this->detailPembelian[$bahan->id] as $detail) {
                    if ($isFirstRow) {
                        $exportData[] = [
                            $bahan->nama_bahan,
                            $bahan->jumlah_stok,
                            $bahan->satuan,
                            \Carbon\Carbon::parse($detail->tanggal_pembelian)->format('Y-m-d'),
                            $detail->nama_supplier,
                            $detail->jumlah,
                            $detail->subtotal
                        ];
                        $isFirstRow = false;
                    } else {
                        $exportData[] = [
                            '',
                            '',
                            '',
                            \Carbon\Carbon::parse($detail->tanggal_pembelian)->format('Y-m-d'),
                            $detail->nama_supplier,
                            $detail->jumlah,
                            $detail->subtotal
                        ];
                    }
                }
            } else {
                $exportData[] = [
                    $bahan->nama_bahan,
                    $bahan->jumlah_stok,
                    $bahan->satuan,
                    '',
                    '',
                    '',
                    ''
                ];
            }
        }

        return $exportData;
    }

    public function title(): string
    {
        return 'Stok & Pembelian ' . $this->tanggal_mulai . ' - ' . $this->tanggal_selesai;
    }
}
