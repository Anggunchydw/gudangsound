<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;
use App\Admin\Controllers\BarangController;
use App\Admin\Controllers\PaketController;
use App\Admin\Controllers\PenyewaanController;
use App\Admin\Controllers\PemasukanController;
use App\Admin\Controllers\PengeluaranController;
use App\Admin\Controllers\RekapKeuanganController;
use App\Admin\Controllers\PenggunaController;
use App\Admin\Controllers\PenugasanController;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('barang', BarangController::class);
    $router->resource('paket', PaketController::class);
    $router->resource('penyewaan', PenyewaanController::class);
    $router->resource('pemasukan', PemasukanController::class);
    $router->resource('pengeluaran', PengeluaranController::class);
    $router->resource('pengguna', PenggunaController::class);
    $router->resource('penugasan', PenugasanController::class);

    // Google Calendar OAuth
    $router->get(
        'google/login',
        'GoogleAuthController@login'
    );

    $router->get(
        'google/callback',
        'GoogleAuthController@callback'
    );
    $router->get(
        'google/test',
        'GoogleAuthController@testCalendar'
    );

    Route::get(
        'penyewaan/{id}/cancel',
        [PenyewaanController::class, 'cancel']
    );

    Route::get(
        'penyewaan/{id}/cetak',
        [PenyewaanController::class, 'cetak']
    )->name('admin.penyewaan.cetak');

    Route::post(
        'penyewaan/{id}/pembayaran',
        [PenyewaanController::class, 'simpanPembayaran']
    );

    Route::get(
        'rekap-keuangan',
        [RekapKeuanganController::class, 'index']
    );
    Route::get(
        'rekap-keuangan/cetak',
        [RekapKeuanganController::class, 'cetak']
    );
    $router->get('Jadwal-Acara', 'JadwalAcaraController@index');
    $router->get('Jadwal-Acara/events', 'JadwalAcaraController@events');

    $router->get(
        'kondisi-barang',
        'KondisiBarangController@index'
    );

    $router->get(
        'kondisi-barang/{penugasan}/input',
        'KondisiBarangController@input'
    );

    $router->post(
        'kondisi-barang/{penugasan}/simpan',
        'KondisiBarangController@simpan'
    );
});
