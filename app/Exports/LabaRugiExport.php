<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LabaRugiExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.export.laba-rugi-export', [
            'laporan' => $this->data['laporan'],
            'namaOutlet' => $this->data['namaOutlet'],
            'tanggal_mulai' => $this->data['tanggal_mulai'],
            'tanggal_selesai' => $this->data['tanggal_selesai'],
        ]);
    }

    public function title(): string
    {
        return 'Laporan Laba Rugi ' . $this->data['tanggal_mulai'] . ' - ' . $this->data['tanggal_selesai'];
    }
}
