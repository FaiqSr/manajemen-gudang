<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendapatanReportExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.export.pendapatan-export', [
            'laporanPendapatan' => $this->data['laporanPendapatan'],
            'namaOutlet' => $this->data['namaOutlet'],
            'tanggal_mulai' => $this->data['tanggal_mulai'],
            'tanggal_selesai' => $this->data['tanggal_selesai'],
        ]);
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }
}
