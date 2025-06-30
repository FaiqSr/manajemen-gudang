@extends('layout.main')
@section('title', 'Rekonsiliasi Bank')

@section('breadcrums')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Rekonsiliasi Bank</h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Parameter Rekonsiliasi</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('rekonsiliasi.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3 form-group">
                        <label>Pilih Akun Bank</label>
                        <select name="id_akun" class="form-control" required>
                            <option value="">-- Pilih Akun --</option>
                            @foreach ($akunKasBank as $akun)
                                <option value="{{ $akun->id }}" {{ $akun->id == $id_akun_terpilih ? 'selected' : '' }}>
                                    {{ $akun->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Rekonsiliasi per Tanggal</label>
                        <input type="date" name="per_tanggal" class="form-control" value="{{ $per_tanggal_terpilih }}"
                            required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Saldo Akhir Menurut Rekening Koran (Rp)</label>
                        <input type="number" step="any" name="saldo_bank" id="saldo_akhir_bank" class="form-control"
                            value="{{ $saldo_bank_terpilih }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <button type="submit" class="btn btn-primary btn-block">Mulai</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($akunTerpilih)
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaksi Menurut Catatan Perusahaan (Buku)</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th width="10%">Ceklis [✓]</th>
                                    <th width="20%">Tanggal</th>
                                    <th>Keterangan</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiBuku as $trx)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input rek-item"
                                                data-debit="{{ $trx->debit }}" data-kredit="{{ $trx->kredit }}">
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d-m-Y') }}</td>
                                        <td>{{ $trx->keterangan }}</td>
                                        <td class="text-right">{{ number_format($trx->debit, 0, ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($trx->kredit, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada transaksi pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Kertas Kerja Rekonsiliasi</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th colspan="2" class="bg-light">Saldo Menurut Bank</th>
                            </tr>
                            <tr>
                                <td>Saldo akhir rekening koran</td>
                                <td class="text-right" id="saldo-bank-display">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="pl-4">(+) Setoran dalam Perjalanan</td>
                                <td class="text-right" id="setoran-transit">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="pl-4">(-) Cek Beredar</td>
                                <td class="text-right" id="cek-beredar">(Rp 0)</td>
                            </tr>
                            <tr class="bg-secondary">
                                <th>Saldo Bank Disesuaikan</th>
                                <th class="text-right" id="saldo-bank-final">Rp 0</th>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <th colspan="2" class="bg-light">Saldo Menurut Perusahaan (Buku)</th>
                            </tr>
                            <tr>
                                <td>Saldo akhir di sistem</td>
                                <td class="text-right">Rp {{ number_format($saldoBuku, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="pl-4">(+) Jasa Giro / Pendapatan Bunga</td>
                                <td class="text-right">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="pl-4">(-) Biaya Administrasi Bank</td>
                                <td class="text-right">(Rp 0)</td>
                            </tr>
                            <tr class="bg-secondary">
                                <th>Saldo Buku Disesuaikan</th>
                                <th class="text-right">Rp {{ number_format($saldoBuku, 0, ',', '.') }}</th>
                            </tr>
                        </table>
                        <a href="{{ url('jurnal-umum/create') }}" target="_blank"
                            class="btn btn-outline-primary btn-block mt-3">Buat Jurnal Penyesuaian (Biaya Admin/Bunga)</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);

            const saldoAkhirBankInput = document.getElementById('saldo_akhir_bank');
            const saldoBankDisplay = document.getElementById('saldo-bank-display');
            const setoranTransitEl = document.getElementById('setoran-transit');
            const cekBeredarEl = document.getElementById('cek-beredar');
            const saldoBankFinalEl = document.getElementById('saldo-bank-final');
            const rekItems = document.querySelectorAll('.rek-item');

            function calculateRekonsiliasi() {
                let totalDebitBuku = 0;
                let totalKreditBuku = 0;
                let totalDebitBukuCleared = 0;
                let totalKreditBukuCleared = 0;

                rekItems.forEach(item => {
                    const debit = parseFloat(item.dataset.debit) || 0;
                    const kredit = parseFloat(item.dataset.kredit) || 0;

                    totalDebitBuku += debit;
                    totalKreditBuku += kredit;

                    if (item.checked) {
                        totalDebitBukuCleared += debit;
                        totalKreditBukuCleared += kredit;
                    }
                });

                const setoranDalamPerjalanan = totalDebitBuku - totalDebitBukuCleared;
                const cekBeredar = totalKreditBuku - totalKreditBukuCleared;
                const saldoAkhirBank = parseFloat(saldoAkhirBankInput.value) || 0;
                const saldoBankDisesuaikan = saldoAkhirBank + setoranDalamPerjalanan - cekBeredar;

                saldoBankDisplay.textContent = formatRupiah(saldoAkhirBank);
                setoranTransitEl.textContent = formatRupiah(setoranDalamPerjalanan);
                cekBeredarEl.textContent = '(' + formatRupiah(cekBeredar) + ')';
                saldoBankFinalEl.textContent = formatRupiah(saldoBankDisesuaikan);
            }

            rekItems.forEach(item => {
                item.addEventListener('change', calculateRekonsiliasi);
            });

            saldoAkhirBankInput.addEventListener('input', calculateRekonsiliasi);

            calculateRekonsiliasi();
        });
    </script>
@endsection
