<?php

namespace App\Admin\Controllers;

use App\Models\Penyewaan;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;

class JadwalAcaraController extends AdminController
{
    public function index(Content $content)
    {
        // Load FullCalendar
        Admin::css('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/main.min.css');
        Admin::js('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js');

        // CSS milik sendiri
        Admin::css(asset('css/jadwal-acara.css'));

        $totalAcara = Penyewaan::whereMonth(
            'tanggal_mulai',
            now()->month
        )->count();

        $belumLunas = Penyewaan::whereMonth(
            'tanggal_mulai',
            now()->month
        )
            ->where('status_pembayaran', 'DP')
            ->count();

        $agendaHariIni = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang'
        ])
            ->whereDate('tanggal_mulai', today())
            ->get();

        $akanDatang = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang'
        ])
            ->where('tanggal_mulai', '>', today())
            ->orderBy('tanggal_mulai')
            ->take(5)
            ->get();

        $agendaHariIni->each(function ($item) {

            $paket = [];

            foreach ($item->detailPaket as $d) {
                if ($d->paket) {
                    $paket[] = $d->paket->nama_paket;
                }
            }

            foreach ($item->detailBarang as $d) {
                if ($d->barang) {
                    $paket[] = $d->barang->nama_barang;
                }
            }

            $item->paket_barang = implode(', ', $paket);
        });

        $akanDatang->each(function ($item) {

            $paket = [];

            foreach ($item->detailPaket as $d) {
                if ($d->paket) {
                    $paket[] = $d->paket->nama_paket;
                }
            }

            foreach ($item->detailBarang as $d) {
                if ($d->barang) {
                    $paket[] = $d->barang->nama_barang;
                }
            }

            $item->paket_barang = implode(', ', $paket);
        });

        return $content
            ->title('Jadwal Acara')
            ->body(view(
                'admin.jadwal.index',
                compact(
                    'totalAcara',
                    'belumLunas',
                    'agendaHariIni',
                    'akanDatang'
                )
            ));
    }
    public function events()
    {
        $events = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang'
        ])->get()->map(function ($item) {

            if ($item->status_penyewaan == 'dibatalkan') {
                $color = '#dc3545';
            } elseif ($item->status_pembayaran == 'Lunas') {
                $color = '#16a34a';
            } else {
                $color = '#f59e0b';
            }
            $paket = [];

            foreach ($item->detailPaket as $detail) {

                if ($detail->paket) {

                    $paket[] =
                        '•' .
                        $detail->paket->nama_paket .
                        ' (x' .
                        $detail->jumlah_paket .
                        ')';
                }
            }

            foreach ($item->detailBarang as $detail) {

                if ($detail->barang) {

                    $paket[] =
                        '• ' .
                        $detail->barang->nama_barang .
                        ' (x' .
                        $detail->jumlah_barang .
                        ')';
                }
            }

            return [

                'id'    => $item->id,

                'title' => $item->nama_penyewa,

                'start' => $item->tanggal_mulai,

                /*
             FullCalendar menganggap end exclusive,
             sehingga harus ditambah 1 hari
            */
                'end'   => date(
                    'Y-m-d',
                    strtotime($item->tanggal_selesai . ' +1 day')
                ),

                'backgroundColor' => $color,

                'borderColor' => $color,
                // Data tambahan
                'extendedProps' => [

                    'lokasi' => $item->lokasi,

                    'status' => $item->status_pembayaran,

                    'mulai' => $item->tanggal_mulai,

                    'selesai' => $item->tanggal_selesai,

                    'paket' => implode('<br>', $paket),

                ],

            ];
        });

        return response()->json($events);
    }
}
