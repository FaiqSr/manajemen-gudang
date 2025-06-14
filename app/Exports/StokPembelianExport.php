<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StokPembelianExport implements FromView, WithTitle, ShouldAutoSize
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

    public function view(): View
    {
        return view('laporan.export.stok-pembelian-export', [
            'daftarBahan' => $this->daftarBahan,
            'detailPembelian' => $this->detailPembelian,
        ]);
    }

    public function title(): string
    {
        return 'Laporan Stok & Pembelian ' . $this->tanggal_mulai . ' - ' . $this->tanggal_selesai;
    }
}
