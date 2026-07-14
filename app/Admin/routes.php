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
});
