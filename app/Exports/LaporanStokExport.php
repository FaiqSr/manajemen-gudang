<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanStokExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.export.stok-export', [
            'stokGudang' => $this->data['stokGudang'],
            'stokOutlet' => $this->data['stokOutlet'],
            'namaOutlet' => $this->data['namaOutlet'],
        ]);
    }

    public function title(): string
    {
        return 'Laporan Stok Keseluruhan';
    }
}
