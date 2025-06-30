<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - FINANCE</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/dist/css/adminlte.min.css">
    <link rel="stylesheet"
        href="{{ asset('/public/adminlte') }}/plugins/pace-progress/themes/black/pace-theme-flat-top.css">
    <link rel="icon" href="{{ asset('/public/adminlte') }}/dist/img/logo.png" type="image/png" sizes="16x16">
    <style>
        .main-sidebar {
            background-color: #2a375c !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini pace-primary">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ url('dashboard') }}" class="nav-link">Home</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown show">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true" class="nav-link dropdown-toggle">
                        <i class="fas fa-user-circle mr-2 text-lg"></i>
                        <span class="hidden-xs">{{ ucfirst(session('nama_lengkap')) }}</span>
                    </a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                        style="left: 0px; right: inherit;">
                        <li><a href="{{ url('profil') }}" class="dropdown-item"><i
                                    class="nav-icon fas fa-user-secret"></i> Profil</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a href="{{ url('logout') }}" class="dropdown-item"><i
                                    class="nav-icon fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link text-center">
                <span class="brand-text text-white">FINANCE</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ url('public/img/profil/men.png') }}" class="img-circle elevation-2">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ ucfirst(session('nama_lengkap')) }}<br><span
                                class="small">{{ session('email') }}</span></a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview"
                        role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ url('dashboard') }}"
                                class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('gudang/stok') }}"
                                class="nav-link {{ request()->is('gudang/stok') ? 'active' : '' }}">
                                <p>Riwayat Stok</p>
                            </a>
                        </li>

                        @if (session('id_role') == 1)
                            <li class="nav-header">MASTER DATA</li>
                            <li
                                class="nav-item {{ request()->is('akun*') || request()->is('satuan*') || request()->is('bahan*') || request()->is('produk*') || request()->is('supplier/index') || request()->is('outlet') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->is('akun*') || request()->is('satuan*') || request()->is('bahan*') || request()->is('produk*') || request()->is('supplier/index') || request()->is('outlet') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-database"></i>
                                    <p>Data Master <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ url('akun') }}"
                                            class="nav-link {{ request()->is('akun*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Daftar Akun</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('supplier/index') }}"
                                            class="nav-link {{ request()->is('supplier/index') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Supplier</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('outlet') }}"
                                            class="nav-link {{ request()->is('outlet') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Outlet</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('satuan') }}"
                                            class="nav-link {{ request()->is('satuan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Satuan</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('bahan') }}"
                                            class="nav-link {{ request()->is('bahan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Bahan Baku</p>
                                        </a></li>
                                    {{-- <li class="nav-item"><a href="{{ url('produk') }}"
                                            class="nav-link {{ request()->is('produk*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Produk</p>
                                        </a></li> --}}
                                </ul>
                            </li>
                        @endif

                        <li class="nav-header">TRANSAKSI</li>
                        <li
                            class="nav-item {{ request()->is('pembelian*') || request()->is('penjualan*') || request()->is('gudang/distribusi') || request()->is('biaya-operasional') || request()->is('transfer-kas*') || request()->is('jurnal-umum*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->is('pembelian*') || request()->is('penjualan*') || request()->is('gudang/distribusi') || request()->is('biaya-operasional') || request()->is('transfer-kas*') || request()->is('jurnal-umum*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Input Transaksi<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if (session('id_role') == 1)
                                    <li class="nav-item"><a href="{{ url('pembelian') }}"
                                            class="nav-link {{ request()->is('pembelian*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Pembelian Bahan Baku</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('penjualan') }}"
                                            class="nav-link {{ request()->is('penjualan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Penjualan</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('gudang/distribusi') }}"
                                            class="nav-link {{ request()->is('gudang/distribusi*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Distribusi Bahan</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('biaya-operasional/') }}"
                                            class="nav-link {{ request()->is('biaya-operasional*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Biaya Operasional</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('transfer-kas') }}"
                                            class="nav-link {{ request()->is('transfer-kas*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Transfer Kas</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('jurnal-umum/create') }}"
                                            class="nav-link {{ request()->is('jurnal-umum*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Jurnal Umum</p>
                                        </a></li>
                                @else
                                    <li class="nav-item"><a href="{{ url('penjualan') }}"
                                            class="nav-link {{ request()->is('penjualan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Penjualan</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('outlet/operasional') }}"
                                            class="nav-link {{ request()->is('outlet/operasional*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Biaya Operasional</p>
                                        </a></li>
                                @endif
                            </ul>
                        </li>

                        @if (session('id_role') == 1)
                            <li
                                class="nav-item {{ request()->is('hutang*') || request()->is('piutang*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->is('hutang*') || request()->is('piutang*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-hand-holding-usd"></i>
                                    <p>Hutang & Piutang<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ url('hutang') }}"
                                            class="nav-link {{ request()->is('hutang*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Hutang Usaha</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('piutang') }}"
                                            class="nav-link {{ request()->is('piutang*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Piutang Usaha</p>
                                        </a></li>
                                </ul>
                            </li>
                            <li class="nav-item {{ request()->is('aset*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->is('aset*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-building"></i>
                                    <p>Manajemen Aset<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ url('aset') }}"
                                            class="nav-link {{ request()->is('aset') && !request()->is('aset/penyusutan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Daftar Aset</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ url('aset/penyusutan') }}"
                                            class="nav-link {{ request()->is('aset/penyusutan*') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Laporan Penyusutan</p>
                                        </a></li>
                                </ul>
                            </li>
                            <li class="nav-header">LAPORAN KEUANGAN</li>
                            <li
                                class="nav-item {{ request()->is('laporan*') || request()->is('rekonsiliasi-bank*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->is('laporan*') || request()->is('rekonsiliasi-bank*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-pie"></i>
                                    <p>Laporan<i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('laporan.laba-rugi') }}"
                                            class="nav-link {{ request()->is('laporan/laba-rugi') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Laba Rugi</p>
                                        </a></li>
                                    <li class="nav-item">
                                        <a href="{{ route('laporan.penjualan') }}"
                                            class="nav-link {{ request()->is('laporan/penjualan') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laporan Penjualan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('laporan.pendapatan') }}"
                                            class="nav-link {{ request()->is('laporan/pendapatan') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laporan Pendapatan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="{{ route('laporan.neraca') }}"
                                            class="nav-link {{ request()->is('laporan/neraca') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Neraca</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ route('laporan.arus-kas') }}"
                                            class="nav-link {{ request()->is('laporan/arus-kas') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Arus Kas</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ route('laporan.buku-besar') }}"
                                            class="nav-link {{ request()->is('laporan/buku-besar') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Buku Besar</p>
                                        </a></li>
                                    <li class="nav-item">
                                        <a href="{{ route('laporan.distribusi') }}"
                                            class="nav-link {{ request()->is('laporan/distribusi') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laporan Distribusi</p>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="{{ route('laporan.hutang') }}"
                                            class="nav-link {{ request()->is('laporan/hutang') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Laporan Hutang</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ route('laporan.piutang') }}"
                                            class="nav-link {{ request()->is('laporan/piutang') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Laporan Piutang</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ route('laporan.ringkasan') }}"
                                            class="nav-link {{ request()->is('laporan/ringkasan') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Ringkasan Pendapatan</p>
                                        </a></li>
                                    <li class="nav-item"><a href="{{ route('laporan.stok-pembelian') }}"
                                            class="nav-link {{ request()->is('laporan/stok-pembelian') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Stok & Pembelian</p>
                                        </a></li>
                                    <li class="nav-item">
                                        <a href="{{ route('laporan.stok-outlet') }}"
                                            class="nav-link {{ request()->is('laporan/stok-outlet') ? 'active' : '' }}">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Laporan Stok Outlet</p>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a href="{{ route('rekonsiliasi.index') }}"
                                            class="nav-link {{ request()->is('rekonsiliasi-bank') ? 'active' : '' }}"><i
                                                class="far fa-circle nav-icon"></i>
                                            <p>Rekonsiliasi Bank</p>
                                        </a></li>
                                </ul>
                            </li>
                            <li class="nav-header">SISTEM</li>
                            <li class="nav-item">
                                <a href="{{ url('user') }}"
                                    class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>Manajemen User</p>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ url('logout') }}" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                <p class="text-danger">Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    @yield('breadcrums')
                </div>
            </section>
            <section class="content">
                @yield('content')
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline"></div>
            <strong>Copyright &copy; 2025 <a href="#">FINANCE</a>.</strong> All rights reserved.
        </footer>
    </div>

    <script src="{{ asset('/public/adminlte') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/jszip/jszip.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/toastr/toastr.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/pace-progress/pace.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/dist/js/adminlte.min.js"></script>

    <script>
        $(function() {
            $('#table1').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
            $('.select2').select2()
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });
    </script>
    @yield('script')
</body>

</html>
