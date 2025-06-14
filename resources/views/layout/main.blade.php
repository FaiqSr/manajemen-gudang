<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - FINANCE</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('/public/adminlte') }}/plugins/fontawesome-free/css/all.min.css">


    <!-- DataTables -->
    <!-- Select2 -->
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



    <!-- pace-progress -->
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
                        <span
                            class="hidden-xs">{{ ucfirst(DB::table('tbl_user')->find(session()->get('id_user'))->nama_lengkap) }}</span>
                    </a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow"
                        style="left: 0px; right: inherit;">
                        <li>
                            <a href="{{ url('profil') }}" class="dropdown-item">
                                <i class="nav-icon fas fa-user-secret"></i> Profil
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a href="{{ url('logout') }}" class="dropdown-item">
                                <i class="nav-icon fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>

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
                        <a href="#"
                            class="d-block">{{ ucfirst(DB::table('tbl_user')->find(session()->get('id_user'))->nama_lengkap) }}
                            <br>
                            <span
                                class="small">{{ DB::table('tbl_user')->find(session()->get('id_user'))->email }}</span>
                        </a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-header">Master</li>
                        <li class="nav-item">
                            <a href="{{ url('satuan') }}"
                                class="nav-link {{ request()->is('satuan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Satuan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('produk') }}"
                                class="nav-link {{ request()->is('produk') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Produk
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">Manajemen Supplier & Pembelian</li>
                        <li class="nav-item">
                            <a href="{{ url('supplier/index') }}"
                                class="nav-link {{ request()->is('supplier/index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Supplier
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('supplier/bahan') }}"
                                class="nav-link {{ request()->is('supplier/bahan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Bahan Baku
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('supplier/pembelian') }}"
                                class="nav-link {{ request()->is('supplier/pembelian') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Pembelian
                                </p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ url('supplier/laporan') }}"
                                class="nav-link {{ request()->is('supplier/laporan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Laporan Pembelian
                                </p>
                            </a>
                        </li> --}}
                        <li class="nav-header">Manajemen Gudang</li>
                        <li class="nav-item">
                            <a href="{{ url('gudang/stok') }}"
                                class="nav-link {{ request()->is('gudang/stok') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Stok Bahan Baku
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('gudang/distribusi') }}"
                                class="nav-link {{ request()->is('gudang/distribusi') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Distribusi Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('gudang/stok-terkini') }}"
                                class="nav-link {{ request()->is('gudang/stok-terkini') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Stok Terkini
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">Manajemen Outlet</li>
                        <li class="nav-item">
                            <a href="{{ url('outlet/') }}"
                                class="nav-link {{ request()->is('outlet') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Outlet
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('outlet/distribusi') }}"
                                class="nav-link {{ request()->is('outlet/distribusi') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Distribusi Bahan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('outlet/operasional') }}"
                                class="nav-link {{ request()->is('outlet/operasional') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Biaya Operasional
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">Penjualan dan Pemasukan</li>
                        <li class="nav-item">
                            <a href="{{ url('penjualan/') }}"
                                class="nav-link {{ request()->is('penjualan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Penjualan per Outlet
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('pendapatan/') }}"
                                class="nav-link {{ request()->is('pendapatan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Laporan Pendapatan
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('transfer-kas') }}"
                                class="nav-link {{ request()->is('transfer-kas') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Arus Kas ke Pusat
                                </p>
                            </a>
                        </li>
                        <li class="nav-header">Laporan</li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/laba-rugi') }}"
                                class="nav-link {{ request()->is('laporan/laba-rugi') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Laba per Outlet
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/arus-kas') }}"
                                class="nav-link {{ request()->is('laporan/arus-kas') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Arus Kas
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/ringkasan') }}"
                                class="nav-link {{ request()->is('laporan/ringkasan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Ringkasan Pendapatan Vs Biaya Operasional
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/stok-pembelian') }}"
                                class="nav-link {{ request()->is('laporan/stok-pembelian') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Stok & Pembelian
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/neraca') }}"
                                class="nav-link {{ request()->is('laporan/neraca') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Neraca
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan/buku-besar') }}"
                                class="nav-link {{ request()->is('laporan/buku-besar') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Buku Besar
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">Manajemen Asset</li>
                        <li class="nav-item">
                            <a href="{{ url('aset') }}"
                                class="nav-link {{ request()->is('aset') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Asset
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('aset/penyusutan') }}"
                                class="nav-link {{ request()->is('aset/penyusutan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Penyusutan
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">Logout</li>
                        <li class="nav-item">
                            <a href="{{ url('logout') }}" class="nav-link text-danger">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
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

            <div class="float-right d-none d-sm-inline">
            </div>

            <strong>Copyright &copy; 2025 <a href="#">FINANCE</a>.</strong> All rights
            reserved.
        </footer>
    </div>



    <script src="{{ asset('/public/adminlte') }}/plugins/jquery/jquery.min.js"></script>

    <script src="{{ asset('/public/adminlte') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('/public/adminlte') }}/dist/js/adminlte.min.js"></script>

    <!-- DataTables  & Plugins -->
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
    <!-- Toastr -->
    <script src="{{ asset('/public/adminlte') }}/plugins/toastr/toastr.min.js"></script>
    <script src="{{ asset('/public/adminlte') }}/plugins/select2/js/select2.full.min.js"></script>
    <!-- pace-progress -->
    <script src="{{ asset('/public/adminlte') }}/plugins/pace-progress/pace.min.js"></script>



    <script>
        $(function() {
            $('#table1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });

            //Initialize Select2 Elements
            $('.select2').select2()
            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });
    </script>

    @yield('script')

</body>

</html>
