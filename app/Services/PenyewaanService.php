<?php

namespace App\Services;

use Dcat\Admin\Form;
use App\Services\InventoryService;
use App\Models\Penyewaan;
use App\Services\PemasukanService;

class PenyewaanService
{
    public static function validate(Form $form)
    {
        $penyewaan = $form->model();

        // Tidak boleh edit selesai / batal
        if ($penyewaan->exists) {

            $status = $penyewaan->status_sekarang;

            if (in_array($status, ['selesai', 'dibatalkan'])) {

                throw new \Exception(
                    "Penyewaan dengan status {$status} tidak dapat diedit."
                );
            }
        }

        $totalHarga = (float) str_replace(',', '', $form->total_harga);

        $uangMuka = (float) str_replace(
            ',',
            '',
            request('uang_muka')
        );

        $form->uang_muka = $uangMuka;

        $detailBarang = request()->input('detailBarang', []);

        $detailPaket = request()->input('detailPaket', []);

        // Validasi tanggal
        if ($form->tanggal_selesai < $form->tanggal_mulai) {

            throw new \Exception(
                'Tanggal selesai tidak boleh sebelum tanggal mulai.'
            );
        }

        // Minimal memilih barang / paket
        $detailBarang = collect(request()->input('detailBarang', []))
            ->filter(function ($item) {
                return !empty($item['barang_id']) && empty($item['_remove_']);
            })
            ->values()
            ->all();

        $detailPaket = collect(request()->input('detailPaket', []))
            ->filter(function ($item) {
                return !empty($item['paket_id']) && empty($item['_remove_']);
            })
            ->values()
            ->all();
        if (empty($detailBarang) && empty($detailPaket)) {
            throw new \Exception(
                'Minimal pilih satu paket atau satu barang.'
            );
        }

        //Barang tidak boleh dobel
        $barangIds = [];

        foreach ($detailBarang as $item) {

            if (empty($item['barang_id'])) {
                continue;
            }

            if (in_array($item['barang_id'], $barangIds)) {

                throw new \Exception(
                    'Barang yang sama tidak boleh dipilih lebih dari satu kali.'
                );
            }

            $barangIds[] = $item['barang_id'];
        }

        // Paket tidak boleh dobel
        $paketIds = [];

        foreach ($detailPaket as $item) {

            if (empty($item['paket_id'])) {
                continue;
            }

            if (in_array($item['paket_id'], $paketIds)) {

                throw new \Exception(
                    'Paket yang sama tidak boleh dipilih lebih dari satu kali.'
                );
            }

            $paketIds[] = $item['paket_id'];
        }


        // DP
        if ($uangMuka < 0) {

            throw new \Exception(
                'Uang muka (DP) tidak boleh bernilai negatif.'
            );
        }

        if ($uangMuka > $totalHarga) {

            throw new \Exception(
                'Uang muka (DP) tidak boleh melebihi total harga.'
            );
        }

        $form->status_pembayaran =
            $uangMuka >= $totalHarga
            ? 'Lunas'
            : 'DP';

        // VALIDASI STOK
        InventoryService::checkAvailability(

            $form->tanggal_mulai,

            $form->tanggal_selesai,

            $detailBarang,

            $detailPaket,

            $form->model()->id
        );
    }
    public static function buatPembayaranAwal($id)
    {
        $penyewaan = Penyewaan::findOrFail($id);

        if ($penyewaan->uang_muka <= 0) {
            return;
        }

        PemasukanService::simpan(
            $penyewaan,
            $penyewaan->uang_muka,
            $penyewaan->status_pembayaran
        );
    }
}
