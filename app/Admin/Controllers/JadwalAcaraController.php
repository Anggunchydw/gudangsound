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

        Admin::css(asset('css/jadwal-acara.css'));

        $user = Admin::user();

        $query = Penyewaan::query()
            ->where('status_penyewaan', '<>', 'dibatalkan');

        // Pegawai hanya melihat jadwal miliknya
        if ($user->isRole('pegawai')) {

            $query->whereHas('penugasan.pegawai', function ($q) use ($user) {

                $q->where('admin_users.id', $user->id);
            });
        }

        $totalAcara = (clone $query)
            ->whereMonth('tanggal_mulai', now()->month)
            ->count();

        $belumLunas = (clone $query)
            ->whereMonth('tanggal_mulai', now()->month)
            ->where('status_pembayaran', 'DP')
            ->count();

        $agendaHariIni = (clone $query)
            ->with([
                'detailPaket.paket',
                'detailBarang.barang'
            ])
            ->whereDate('tanggal_mulai', today())
            ->get();

        $akanDatang = (clone $query)
            ->with([
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
        $user = Admin::user();

        $query = Penyewaan::with([
            'detailPaket.paket',
            'detailBarang.barang',
            'penugasan.pegawai'
        ])
            ->where('status_penyewaan', '<>', 'dibatalkan');

        // Pegawai hanya melihat event yang menjadi penugasannya
        if ($user->isRole('pegawai')) {

            $query->whereHas('penugasan.pegawai', function ($q) use ($user) {
                $q->where('admin_users.id', $user->id);
            });
        }

        $events = $query
            ->get()
            ->map(function ($item) {

                $color = $item->status_pembayaran == 'Lunas'
                    ? '#388E3C'
                    : '#F59E0B';

                $paketBarang = [];

                foreach ($item->detailPaket as $detail) {

                    if ($detail->paket) {

                        $paketBarang[] = [
                            'nama' => $detail->paket->nama_paket,
                            'jumlah' => $detail->jumlah_paket,
                            'tipe' => 'Paket',
                        ];
                    }
                }

                foreach ($item->detailBarang as $detail) {

                    if ($detail->barang) {

                        $paketBarang[] = [
                            'nama' => $detail->barang->nama_barang,
                            'jumlah' => $detail->jumlah_barang,
                            'tipe' => 'Barang',
                        ];
                    }
                }

                $pegawai = [];

                if ($item->penugasan) {

                    foreach ($item->penugasan->pegawai as $pegawaiUser) {

                        if ($pegawaiUser->name) {
                            $pegawai[] = $pegawaiUser->name;
                        }
                    }
                }

                $pegawai = array_values(array_unique($pegawai));

                return [

                    'id' => $item->id,

                    'title' => $item->nama_penyewa,

                    'start' => $item->tanggal_mulai,

                    'end' => date(
                        'Y-m-d',
                        strtotime($item->tanggal_selesai . ' +1 day')
                    ),

                    'backgroundColor' => $color,

                    'borderColor' => $color,

                    'extendedProps' => [

                        'lokasi' => $item->lokasi,

                        'keterangan' => $item->keterangan,

                        'status' => $item->status_pembayaran,

                        'mulai' => $item->tanggal_mulai,

                        'selesai' => $item->tanggal_selesai,

                        'paket_barang' => $paketBarang,

                        'pegawai' => $pegawai,
                    ],
                ];
            });

        return response()->json($events);
    }
}
