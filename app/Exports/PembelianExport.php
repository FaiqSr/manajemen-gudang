<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PembelianExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('supplier.export.pembelian-export', [
            'pembelians' => $this->data['pembelians'],
            'groupedDetails' => $this->data['groupedDetails'],
            'tanggal_mulai' => $this->data['tanggal_mulai'],
            'tanggal_selesai' => $this->data['tanggal_selesai'],
        ]);
    }

    public function title(): string
    {
        return 'Riwayat Pembelian ' . $this->data['tanggal_mulai'] . ' - ' . $this->data['tanggal_selesai'];
    }
}
