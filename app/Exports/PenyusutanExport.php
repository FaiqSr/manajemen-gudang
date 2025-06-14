<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenyusutanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $asetGrouped;
    protected $bulan_terpilih;
    protected $tahun_terpilih;

    public function __construct($asetGrouped, $bulan, $tahun)
    {
        $this->asetGrouped = $asetGrouped;
        $this->bulan_terpilih = $bulan;
        $this->tahun_terpilih = $tahun;
    }

    public function view(): View
    {
        return view('aset.penyusutan-export', [
            'asetGrouped' => $this->asetGrouped
        ]);
    }

    public function title(): string
    {
        $namaBulan = \Carbon\Carbon::create()->month($this->bulan_terpilih)->format('F');
        return 'Laporan Penyusutan ' . $namaBulan . ' ' . $this->tahun_terpilih;
    }
}
