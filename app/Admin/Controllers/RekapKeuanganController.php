<?php

namespace App\Admin\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Dcat\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RekapKeuanganController extends Controller
{
    public function index(Content $content, Request $request)
    {
        $mulai  = $request->get('mulai');
        $sampai = $request->get('sampai');

        // ==========================
        // DATA PEMASUKAN
        // ==========================
        $pemasukan = Pemasukan::when($mulai, function ($q) use ($mulai) {
                $q->whereDate('tanggal_masuk', '>=', $mulai);
            })
            ->when($sampai, function ($q) use ($sampai) {
                $q->whereDate('tanggal_masuk', '<=', $sampai);
            })
            ->get();

        // ==========================
        // DATA PENGELUARAN
        // ==========================
        $pengeluaran = Pengeluaran::when($mulai, function ($q) use ($mulai) {
                $q->whereDate('tanggal_pengeluaran', '>=', $mulai);
            })
            ->when($sampai, function ($q) use ($sampai) {
                $q->whereDate('tanggal_pengeluaran', '<=', $sampai);
            })
            ->get();

        // ==========================
        // GABUNGKAN
        // ==========================
        $data = [];

        foreach ($pemasukan as $item) {

            $data[] = [
                'tanggal'    => $item->tanggal_masuk,
                'tipe'       => 'Pemasukan',
                'keterangan' => $item->keterangan,
                'masuk'      => $item->jumlah,
                'keluar'     => 0,
            ];
        }

        foreach ($pengeluaran as $item) {

            $data[] = [
                'tanggal'    => $item->tanggal_pengeluaran,
                'tipe'       => 'Pengeluaran',
                'keterangan' => $item->keterangan,
                'masuk'      => 0,
                'keluar'     => $item->jumlah_pengeluaran,
            ];
        }

        usort($data, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $totalMasuk = $pemasukan->sum('jumlah');
        $totalKeluar = $pengeluaran->sum('jumlah_pengeluaran');

        return $content
            ->title('Rekap Keuangan')
            ->body(view(
                'admin.rekap-keuangan',
                compact(
                    'data',
                    'totalMasuk',
                    'totalKeluar',
                    'mulai',
                    'sampai'
                )
            ));
    }
}
