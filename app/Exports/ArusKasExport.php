<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArusKasExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        $namaOutlet = $this->data['namaOutlet'] ?: 'Semua Outlet (Total)';
        $periode = 'Periode: ' . \Carbon\Carbon::parse($this->data['tanggal_mulai'])->format('d M Y') . ' - ' . \Carbon\Carbon::parse($this->data['tanggal_selesai'])->format('d M Y');

        return [
            ['Laporan Arus Kas'],
            [$namaOutlet],
            [$periode],
            [],
            ['Keterangan', 'Jumlah (Rp)']
        ];
    }

    public function array(): array
    {
        $laporan = $this->data['laporan'];
        $saldoAwal = $this->data['saldoAwal'];

        $net_operasi = $laporan['totals']['masuk_operasi'] - $laporan['totals']['keluar_operasi'];
        $net_investasi = $laporan['totals']['masuk_investasi'] - $laporan['totals']['keluar_investasi'];
        $net_pendanaan = $laporan['totals']['masuk_pendanaan'] - $laporan['totals']['keluar_pendanaan'];
        $kenaikan_bersih = $net_operasi + $net_investasi + $net_pendanaan;
        $saldo_akhir = $saldoAwal + $kenaikan_bersih;

        return [
            ['Saldo Kas Awal Periode', $saldoAwal],
            [],
            ['Arus Kas dari Aktivitas Operasi', ''],
            ['  Penerimaan dari Pelanggan', $laporan['totals']['masuk_operasi']],
            ['  Pembayaran ke Supplier & Beban Operasional', -$laporan['totals']['keluar_operasi']],
            ['Arus Kas Bersih dari Aktivitas Operasi', $net_operasi],
            [],
            ['Arus Kas dari Aktivitas Investasi', ''],
            ['  Penjualan Aset Tetap', $laporan['totals']['masuk_investasi']],
            ['  Pembelian Aset Tetap', -$laporan['totals']['keluar_investasi']],
            ['Arus Kas Bersih dari Aktivitas Investasi', $net_investasi],
            [],
            ['Arus Kas dari Aktivitas Pendanaan', ''],
            ['  Setoran Modal / Penerimaan Pinjaman', $laporan['totals']['masuk_pendanaan']],
            ['  Pembayaran Utang Bank', -$laporan['totals']['keluar_pendanaan']],
            ['Arus Kas Bersih dari Aktivitas Pendanaan', $net_pendanaan],
            [],
            ['Kenaikan (Penurunan) Bersih Kas', $kenaikan_bersih],
            ['SALDO KAS AKHIR PERIODE', $saldo_akhir],
        ];
    }

    public function title(): string
    {
        return 'Laporan Arus Kas';
    }
}
