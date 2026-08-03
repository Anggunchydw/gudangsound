<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use Dcat\Admin\Layout\Content;
use App\Models\KondisiBarang;
use App\Models\Barang;
use Dcat\Admin\Admin;
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
        Admin::css(asset('css/kondisi-barang.css'));
        $penugasan->load([
            'penyewaan.detailBarang.barang',
            'penyewaan.detailPaket.paket.detail.barang',
            'kondisiBarang'
        ]);

        $barang = [];

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

        $kondisi = $penugasan->kondisiBarang->keyBy('barang_id');

        foreach ($barang as &$item) {

            $data = $kondisi->get($item['id']);

            $item['kondisi_sebelum'] = $data->kondisi_sebelum ?? null;
            $item['kondisi_sesudah'] = $data->kondisi_sesudah ?? null;
            $item['jumlah_bermasalah'] = $data->jumlah_bermasalah ?? 0;
            $item['catatan'] = $data->catatan ?? null;
        }

        unset($item);

        return $content
            ->title('Input Kondisi Barang')
            ->body(view(
                'admin.kondisi.input-kondisi',
                [
                    'penugasan' => $penugasan,
                    'barang' => collect($barang)->values(),
                ]
            ));
    }

    public function simpan(Request $request, Penugasan $penugasan)
    {
        foreach ($request->barang as $item) {

            $jumlahBarang = (int) $item['jumlah_barang'];
            $jumlahBermasalah = (int) $item['jumlah_bermasalah'];
            $kondisiBaru = $item['kondisi_sesudah'] ?? null;

            // Jika kondisi baik, paksa jumlah menjadi 0
            if ($kondisiBaru == 'baik') {
                $jumlahBermasalah = 0;
            }

            // Jika rusak/hilang wajib mengisi jumlah
            if (
                in_array($kondisiBaru, ['rusak', 'hilang']) &&
                $jumlahBermasalah <= 0
            ) {
                admin_error(
                    'Gagal',
                    'Masukkan jumlah barang yang rusak atau hilang.'
                );

                return redirect()->to(
                    admin_url('kondisi-barang/' . $penugasan->id . '/input')
                )->withInput();
            }

            // Tidak boleh melebihi jumlah barang
            if ($jumlahBermasalah > $jumlahBarang) {

                admin_error(
                    'Gagal',
                    'Jumlah barang rusak/hilang tidak boleh melebihi jumlah barang yang dibawa.'
                );

                return redirect()->to(
                    admin_url('kondisi-barang/' . $penugasan->id . '/input')
                )->withInput();
            }

            if ($jumlahBermasalah > $jumlahBarang) {

                admin_error(
                    'Gagal',
                    'Jumlah barang rusak/hilang tidak boleh melebihi jumlah barang yang dibawa.'
                );

                return redirect()->to(
                    admin_url('kondisi-barang/' . $penugasan->id . '/input')
                )->withInput();
            }

            $barang = Barang::findOrFail($item['barang_id']);

            $dataLama = KondisiBarang::where(
                'penugasan_id',
                $penugasan->id
            )
                ->where(
                    'barang_id',
                    $item['barang_id']
                )
                ->first();

            $kondisiLama = $dataLama->kondisi_sesudah ?? null;
            $jumlahLama  = (int) ($dataLama->jumlah_bermasalah ?? 0);

            if ($kondisiBaru == 'baik') {

                if (in_array($kondisiLama, ['rusak', 'hilang'])) {

                    $barang->increment(
                        'jumlah_total',
                        $jumlahLama
                    );
                }
            } else {

                $selisih = $jumlahBermasalah - $jumlahLama;

                if ($selisih > 0) {

                    $barang->decrement(
                        'jumlah_total',
                        $selisih
                    );
                } elseif ($selisih < 0) {

                    $barang->increment(
                        'jumlah_total',
                        abs($selisih)
                    );
                }
            }

            KondisiBarang::updateOrCreate(
                [
                    'penugasan_id' => $penugasan->id,
                    'barang_id'    => $item['barang_id'],
                ],
                [
                    'jumlah_barang'      => $jumlahBarang,
                    'jumlah_bermasalah'  => $jumlahBermasalah,
                    'kondisi_sebelum'    => $item['kondisi_sebelum'] ?? null,
                    'kondisi_sesudah'    => $kondisiBaru,
                    'catatan'            => $item['catatan'] ?? null,
                ]
            );
        }

        admin_success(
            'Berhasil',
            'Data kondisi barang berhasil disimpan.'
        );

        return redirect(admin_url('kondisi-barang'));
    }
}
