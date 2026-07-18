<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use Dcat\Admin\Layout\Content;
use App\Models\KondisiBarang;
use App\Models\Barang;
use Dcat\Admin\Http\Controllers\AdminController;

class KondisiBarangController extends AdminController
{

    public function index(Content $content)
    {
        $pegawai = auth('admin')->user();

        $penugasan = Penugasan::with([
            'penyewaan.detailBarang',
            'penyewaan.detailPaket.paket.detail',
            'kondisiBarang'
        ])
            ->whereHas('pegawai', function ($q) use ($pegawai) {
                $q->where('admin_users.id', $pegawai->id);
            })
            ->get();

        foreach ($penugasan as $item) {

            // hitung jumlah barang yang seharusnya diinput
            $barang = [];

            foreach ($item->penyewaan->detailBarang as $detail) {

                $barang[$detail->barang_id] = true;
            }

            foreach ($item->penyewaan->detailPaket as $paket) {

                if (!$paket->paket) {
                    continue;
                }

                foreach ($paket->paket->detail as $detail) {
                    $barang[$detail->barang_id] = true;
                }
            }

            $totalBarang = count($barang);

            $totalInput = $item->kondisiBarang->count();

            $belumLengkap = $item->kondisiBarang
                ->filter(function ($k) {
                    return empty($k->kondisi_sebelum)
                        || empty($k->kondisi_sesudah);
                })
                ->count();

            if ($totalInput == 0) {

                $item->status_input = 'belum';
            } elseif ($totalInput < $totalBarang || $belumLengkap > 0) {

                $item->status_input = 'belum_lengkap';
            } else {

                $item->status_input = 'lengkap';
            }
        }
        return $content
            ->title('Kondisi Barang')
            ->body(view(
                'admin.kondisi.daftar-penugasan',
                compact('penugasan')
            ));
    }

    public function input(Penugasan $penugasan, Content $content)
    {
        $penugasan->load([
            'penyewaan.detailBarang.barang',
            'penyewaan.detailPaket.paket.detail.barang',
            'kondisiBarang'
        ]);

        $barang = [];

        /*
    |--------------------------------------------------------------------------
    | Barang langsung
    |--------------------------------------------------------------------------
    */

        foreach ($penugasan->penyewaan->detailBarang as $detail) {

            if (!$detail->barang) {
                continue;
            }

            $id = $detail->barang->id;

            if (!isset($barang[$id])) {

                $barang[$id] = [
                    'id' => $id,
                    'nama' => $detail->barang->nama_barang,
                    'jumlah' => 0,
                ];
            }

            $barang[$id]['jumlah'] += $detail->jumlah_barang;
        }

        /*
    |--------------------------------------------------------------------------
    | Barang dari paket
    |--------------------------------------------------------------------------
    */

        foreach ($penugasan->penyewaan->detailPaket as $paket) {

            if (!$paket->paket) {
                continue;
            }

            foreach ($paket->paket->detail as $detail) {

                if (!$detail->barang) {
                    continue;
                }

                $id = $detail->barang->id;

                if (!isset($barang[$id])) {

                    $barang[$id] = [
                        'id' => $id,
                        'nama' => $detail->barang->nama_barang,
                        'jumlah' => 0,
                    ];
                }

                $barang[$id]['jumlah'] +=
                    $detail->jumlah * $paket->jumlah_paket;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil data kondisi yang sudah pernah disimpan
    |--------------------------------------------------------------------------
    */

        $kondisi = $penugasan
            ->kondisiBarang
            ->keyBy('barang_id');

        /*
    |--------------------------------------------------------------------------
    | Gabungkan dengan daftar barang
    |--------------------------------------------------------------------------
    */

        foreach ($barang as &$item) {

            if (isset($kondisi[$item['id']])) {

                $item['kondisi_sebelum'] =
                    $kondisi[$item['id']]->kondisi_sebelum;

                $item['kondisi_sesudah'] =
                    $kondisi[$item['id']]->kondisi_sesudah;

                $item['catatan'] =
                    $kondisi[$item['id']]->catatan;
            } else {

                $item['kondisi_sebelum'] = null;
                $item['kondisi_sesudah'] = null;
                $item['catatan'] = null;
            }
        }

        unset($item);

        return $content
            ->title('Input Kondisi Barang')
            ->body(
                view(
                    'admin.kondisi.input-kondisi',
                    [
                        'penugasan' => $penugasan,
                        'barang' => collect($barang)->values(),
                    ]
                )
            );
    }

    public function simpan(Request $request, Penugasan $penugasan)
    {
        // Jumlah barang yang memang harus diinput
        $totalBarang = count($request->barang);

        foreach ($request->barang as $item) {

            $barang = Barang::findOrFail($item['barang_id']);

            /*
        |------------------------------------------------------------
        | Ambil kondisi lama
        |------------------------------------------------------------
        */

            $kondisiSebelumnya = KondisiBarang::where(
                'penugasan_id',
                $penugasan->id
            )
                ->where('barang_id', $item['barang_id'])
                ->value('kondisi_sesudah');

            /*
        |------------------------------------------------------------
        | Simpan / Update kondisi
        |------------------------------------------------------------
        */

            KondisiBarang::updateOrCreate(
                [
                    'penugasan_id' => $penugasan->id,
                    'barang_id'    => $item['barang_id'],
                ],
                [
                    'jumlah_barang'   => $item['jumlah_barang'],
                    'kondisi_sebelum' => $item['kondisi_sebelum'] ?? null,
                    'kondisi_sesudah' => $item['kondisi_sesudah'] ?? null,
                    'catatan'         => $item['catatan'] ?? null,
                ]
            );

            $kondisiBaru = $item['kondisi_sesudah'] ?? null;

            /*
        |------------------------------------------------------------
        | Baik -> Rusak / Hilang
        |------------------------------------------------------------
        */

            if (
                !in_array($kondisiSebelumnya, ['rusak', 'hilang']) &&
                in_array($kondisiBaru, ['rusak', 'hilang'])
            ) {

                $barang->decrement(
                    'jumlah_total',
                    $item['jumlah_barang']
                );
            }

            /*
        |------------------------------------------------------------
        | Rusak / Hilang -> Baik
        |------------------------------------------------------------
        */

            if (
                in_array($kondisiSebelumnya, ['rusak', 'hilang']) &&
                $kondisiBaru == 'baik'
            ) {

                $barang->increment(
                    'jumlah_total',
                    $item['jumlah_barang']
                );
            }
        }

        admin_success(
            'Berhasil',
            'Data kondisi barang berhasil disimpan.'
        );

        return redirect(admin_url('kondisi-barang'));
    }
}
