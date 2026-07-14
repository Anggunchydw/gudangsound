<?php

namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Penyewaan;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        $totalPenyewaan = Penyewaan::count();

        $stokBarang = Barang::all()->sum(function ($barang) {
            return $barang->getStokHariIniAttribute();
        });

        $pendapatan = Pemasukan::whereMonth('tanggal_masuk', now()->month)
            ->whereYear('tanggal_masuk', now()->year)
            ->sum('jumlah');

        $transaksiTerbaru = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang'
        ])
            ->latest()
            ->take(5)
            ->get();

        return $content
            ->header('Dashboard')
            ->description('')
            ->body(view(
                'admin.dashboard',
                compact(
                    'totalPenyewaan',
                    'stokBarang',
                    'pendapatan',
                    'transaksiTerbaru'
                )
            ));
    }
}
