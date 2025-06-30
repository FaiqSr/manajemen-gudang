<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RingkasanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.export.ringkasan-export', [
            'totalPendapatan' => $this->data['totalPendapatan'],
            'totalBiayaOperasional' => $this->data['totalBiayaOperasional'],
            'rincianBiaya' => $this->data['rincianBiaya'],
            'namaOutlet' => $this->data['namaOutlet'],
            'tanggal_mulai' => $this->data['tanggal_mulai'],
            'tanggal_selesai' => $this->data['tanggal_selesai'],
        ]);
    }

    public function title(): string
    {
        return 'Ringkasan Pendapatan vs Biaya ' . $this->data['tanggal_mulai'] . ' - ' . $this->data['tanggal_selesai'];
    }
}
