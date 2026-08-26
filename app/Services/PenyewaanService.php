<?php

namespace App\Services;

use Dcat\Admin\Form;
use App\Models\Penyewaan;
use App\Services\InventoryService;
use App\Services\PemasukanService;
use Illuminate\Support\Facades\DB;

class PenyewaanService
{
    protected static bool $transactionStarted = false;

    public static function validate(Form $form)
    {
        if (!DB::transactionLevel()) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
            DB::beginTransaction();
            self::$transactionStarted = true;
        }

        try {
            $penyewaan = $form->model();

            if ($penyewaan->exists) {
                $status = $penyewaan->status_penyewaan;
                if (in_array($status, ['selesai', 'dibatalkan'])) {
                    throw new \Exception("Penyewaan dengan status {$status} tidak dapat diedit.");
                }
            }

            $totalHarga = (float) str_replace(',', '', $form->total_harga);

            if ($form->isCreating()) {
                $uangMuka = (float) str_replace(',', '', request('uang_muka'));
                $form->uang_muka = $uangMuka;
            } else {
                $uangMuka = (float) $penyewaan->uang_muka;
            }

            if ($form->tanggal_selesai < $form->tanggal_mulai) {
                throw new \Exception('Tanggal selesai tidak boleh sebelum tanggal mulai.');
            }

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
                throw new \Exception('Minimal pilih satu paket atau satu barang.');
            }

            // Validasi duplikasi barang
            $barangIds = array_column($detailBarang, 'barang_id');
            if (count($barangIds) !== count(array_unique($barangIds))) {
                throw new \Exception('Barang yang sama tidak boleh dipilih lebih dari satu kali.');
            }

            // Validasi duplikasi paket
            $paketIds = array_column($detailPaket, 'paket_id');
            if (count($paketIds) !== count(array_unique($paketIds))) {
                throw new \Exception('Paket yang sama tidak boleh dipilih lebih dari satu kali.');
            }

            if ($uangMuka < 0) {
                throw new \Exception('Uang muka (DP) tidak boleh bernilai negatif.');
            }

            if ($uangMuka > $totalHarga) {
                throw new \Exception('Uang muka (DP) tidak boleh melebihi total harga.');
            }

            if ($penyewaan->exists && $uangMuka != $penyewaan->uang_muka) {
                throw new \Exception('Uang muka tidak dapat diubah. Gunakan menu Tambah Pembayaran untuk menambah pembayaran.');
            }

            if ($totalHarga < $uangMuka) {
                throw new \Exception('Total harga tidak boleh lebih kecil dari total pembayaran yang sudah diterima.');
            }

            $form->status_pembayaran = $uangMuka >= $totalHarga ? 'Lunas' : 'DP';

            // Pengecekan stok + LockForUpdate
            InventoryService::checkAvailability(
                $form->tanggal_mulai,
                $form->tanggal_selesai,
                $detailBarang,
                $detailPaket,
                $penyewaan->id
            );
        } catch (\Throwable $e) {
            self::rollbackTransaction();
            throw $e;
        }
    }

    public static function commitTransaction()
    {
        if (self::$transactionStarted && DB::transactionLevel() > 0) {
            DB::commit();
            self::$transactionStarted = false;
        }
    }

    public static function rollbackTransaction()
    {
        if (self::$transactionStarted && DB::transactionLevel() > 0) {
            DB::rollBack();
            self::$transactionStarted = false;
        }
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
