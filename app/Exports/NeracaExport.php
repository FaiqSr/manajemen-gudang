<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NeracaExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $laporan;
    protected $per_tanggal;

    public function __construct(array $laporan, $per_tanggal)
    {
        $this->laporan = $laporan;
        $this->per_tanggal = $per_tanggal;
    }

    public function view(): View
    {
        return view('laporan.export.neraca-export', [
            'laporan' => $this->laporan,
            'per_tanggal' => $this->per_tanggal,
        ]);
    }

    public function title(): string
    {
        return 'Neraca per ' . $this->per_tanggal;
    }
}
