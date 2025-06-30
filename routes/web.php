<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/test', function () {
    return view('test');
});

Route::post('login', 'AuthController@login')->name('login');
Route::get('logout', 'AuthController@logout')->name('logout');

Route::group(['middleware' => 'CheckLoginMiddleware'], function () {
    //Home
    Route::get('dashboard', 'DashboardController@index')->name('dashboard_admin');
    Route::get('profil', 'DashboardController@profil')->name('profil');

    // USER
    Route::get('user', 'UserController@index')->name('user.index');
    Route::get('user/add', 'UserController@create')->name('user.create');
    Route::post('user/add', 'UserController@store')->name('user.store');
    Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
    Route::post('user/edit/{id}', 'UserController@update')->name('user.update');
    Route::get('user/delete/{id}', 'UserController@destroy')->name('user.destroy');

    // AKUN
    Route::get('/akun', 'AkunController@index')->name('akun.index');
    Route::post('/akun', 'AkunController@store')->name('akun.store');
    Route::post('/akun/update/{id}', 'AkunController@update')->name('akun.update');
    Route::get('/akun/delete/{id}', 'AkunController@destroy')->name('akun.destroy');

    // SATUAN
    Route::get('/satuan', 'SatuanController@index')->name('satuan.index');
    Route::post('/satuan', 'SatuanController@store')->name('satuan.store');
    Route::post('/satuan/update/{id}', 'SatuanController@update')->name('satuan.update');
    Route::get('/satuan/delete/{id}', 'SatuanController@destroy')->name('satuan.destroy');

    // USER
    Route::get('pengguna/index', 'PenggunaController@index')->name('pengguna/index');
    Route::get('pengguna/add', 'PenggunaController@create')->name('add');
    Route::post('pengguna/add', 'PenggunaController@add')->name('add');
    Route::get('pengguna/edit/{id}', 'PenggunaController@edit')->name('pengguna/edit');
    Route::post('pengguna/edit', 'PenggunaController@update')->name('pengguna/edit');
    Route::get('pengguna/pengguna/delete/{id}', 'PenggunaController@delete')->name('pengguna/pengguna/delete');

    // SUPPLIER
    Route::get('supplier/index', 'SupplierController@index')->name('supplier/index');
    Route::get('supplier/add', 'SupplierController@create')->name('add');
    Route::post('supplier/add', 'SupplierController@add')->name('add');
    Route::get('supplier/edit/{id}', 'SupplierController@edit')->name('supplier/edit');
    Route::post('supplier/edit', 'SupplierController@update')->name('supplier/edit');
    Route::get('supplier/supplier/delete/{id}', 'SupplierController@delete')->name('supplier/supplier/delete');

    Route::get('pembelian', 'SupplierController@pembelian')->name('pembelian.create');
    Route::post('supplier/addpembelian', 'SupplierController@add_pembelian')->name('pembelian.store');
    Route::post('pembelian/import', 'SupplierController@import')->name('pembelian.import');

    Route::get('bahan', 'SupplierController@bahanBaku')->name('supplier/bahan');
    Route::get('bahan/add', 'SupplierController@addBahanBaku')->name('supplier/bahan/add');
    Route::post('bahan/add', 'SupplierController@createBahanBaku')->name('bahan.add');
    Route::get('bahan/edit/{id}', 'SupplierController@editBahanBaku')->name('bahan.edit');
    Route::post('bahan/edit/{id}', 'SupplierController@updateBahanBaku')->name('bahan.update');

    // GUDANG
    Route::get('gudang/stok', 'GudangController@stok')->name('gudang/stok');
    Route::get('gudang/stok-terkini', 'GudangController@stokTerkini')->name('gudang/stok-terkini');

    // Stok Opname
    Route::get('/stok-opname', 'StokOpnameController@create')->name('stok_opname.create');
    Route::post('/stok-opname', 'StokOpnameController@store')->name('stok_opname.store');
    Route::get('/get-bahan-by-outlet', 'StokOpnameController@getBahanByOutlet')->name('get-bahan-by-outlet');
    Route::post('/stok-opname/import', 'StokOpnameController@import')->name('stok_opname.import');

    // DISTRIBUSI
    Route::get('/distribusi', 'DistribusiController@index')->name('distribusi.index');
    Route::post('/distribusi', 'DistribusiController@store')->name('distribusi.store');
    Route::post('/distribusi/import', 'DistribusiController@import')->name('distribusi.import');

    // OUTLET
    Route::get('outlet', 'OutletController@index')->name('outlet');
    Route::get('outlet/add', 'OutletController@add')->name('outlet/add');
    Route::post('outlet/add', 'OutletController@create')->name('outlet/add');
    Route::get('outlet/edit/{id}', 'OutletController@edit')->name('outlet/edit');
    Route::post('outlet/edit', 'OutletController@update')->name('outlet.update');
    Route::get('outlet/delete/{id}', 'OutletController@delete')->name('outlet.delete');

    Route::get('outlet/stok/{outlet_id}', 'OutletController@stok')->name('outlet.stok');
    Route::get('outlet/stok/edit/{stok_outlet_id}', 'OutletController@editStok')->name('outlet.stok.edit');
    Route::post('outlet/stok/update', 'OutletController@updateStok')->name('outlet.stok.update');

    // Operasional Outlet
    Route::get('/biaya-operasional', 'BiayaOperasionalController@index')->name('biaya.index');
    Route::get('/biaya-operasional/create', 'BiayaOperasionalController@create')->name('biaya.create');
    Route::post('/biaya-operasional', 'BiayaOperasionalController@store')->name('biaya.store');
    Route::get('/biaya-operasional/bayar/{id}', 'BiayaOperasionalController@paymentCreate')->name('biaya.bayar.create');
    Route::post('/biaya-operasional/bayar', 'BiayaOperasionalController@paymentStore')->name('biaya.bayar.store');

    // Penjualan Dan Pendapatan
    Route::get('/penjualan', 'PenjualanController@index')->name('penjualan-bahan.index');
    Route::post('/penjualan', 'PenjualanController@store')->name('penjualan.store');
    Route::get('/get-stok-outlet', 'PenjualanController@getStok')->name('get-stok.outlet');

    Route::get('pendapatan', 'PendapatanController@index')->name('pendapatan');

    Route::get('/transfer-kas', 'ArusKasController@create')->name('transfer-kas.create');
    Route::post('/transfer-kas', 'ArusKasController@store')->name('transfer-kas.store');

    // Laporan
    Route::get('/laporan/laba-rugi', 'LaporanController@showLaba')->name('laporan.laba-rugi');
    Route::get('/laporan/arus-kas', 'LaporanController@showArusKas')->name('laporan.arus-kas');
    Route::get('/laporan/ringkasan', 'LaporanController@showRingkasan')->name('laporan.ringkasan');
    Route::get('/laporan/pembelian', 'LaporanController@showPembelian')->name('laporan.pembelian');
    Route::get('/laporan/neraca', 'LaporanController@showNeraca')->name('laporan.neraca');
    Route::get('/laporan/buku-besar', 'LaporanController@showBukuBesar')->name('laporan.buku-besar');
    Route::get('/laporan/hutang', 'HutangController@laporan')->name('laporan.hutang');
    Route::get('/laporan/piutang', 'PiutangController@laporan')->name('laporan.piutang');
    Route::get('/laporan/distribusi', 'DistribusiController@laporan')->name('laporan.distribusi');
    Route::get('/laporan/penjualan', 'LaporanController@showLaporanPenjualan')->name('laporan.penjualan');
    Route::get('/laporan/pendapatan', 'LaporanController@showLaporanPendapatan')->name('laporan.pendapatan');
    Route::get('/laporan/stok', 'LaporanController@showLaporanStok')->name('laporan.stok');

    // Asset
    Route::get('/aset', 'AssetController@index')->name('aset.index');
    Route::get('/aset/create', 'AssetController@create')->name('aset.create');
    Route::post('/aset', 'AssetController@store')->name('aset.store');
    Route::get('/aset/delete/{id}', 'AssetController@destroy')->name('aset.destroy');
    Route::get('/aset/penyusutan', 'AssetController@showPenyusutanAsset')->name('laporan.penyusutan');

    // HUTANG
    Route::get('/hutang', 'HutangController@index')->name('hutang.index');
    Route::get('/hutang/bayar/{id}', 'HutangController@create')->name('hutang.bayar.create');
    Route::post('/hutang/bayar', 'HutangController@store')->name('hutang.bayar.store');

    // PIUTANG
    Route::get('/piutang', 'PiutangController@index')->name('piutang.index');
    Route::get('/piutang/terima/{id}', 'PiutangController@create')->name('piutang.terima.create');
    Route::post('/piutang/terima', 'PiutangController@store')->name('piutang.terima.store');

    // REKONSILIASI BANK
    Route::get('/rekonsiliasi-bank', 'RekonsiliasiBankController@index')->name('rekonsiliasi.index');

    // JURNAL UMUM
    Route::get('/jurnal-umum/create', 'JurnalUmumController@create')->name('jurnal.create');
    Route::post('/jurnal-umum', 'JurnalUmumController@store')->name('jurnal.store');
});
