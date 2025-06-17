<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PenyusutanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('aset.penyusutan-export', [
            'asetGrouped' => $this->data['asetGrouped'],
            'namaOutlet' => $this->data['namaOutlet'],
            'bulan_terpilih' => $this->data['bulan_terpilih'],
            'tahun_terpilih' => $this->data['tahun_terpilih'],
        ]);
    }

    public function title(): string
    {
        $namaBulan = \Carbon\Carbon::create()->month($this->data['bulan_terpilih'])->format('F');
        return 'Laporan Penyusutan ' . $namaBulan . ' ' . $this->data['tahun_terpilih'];
    }
}
