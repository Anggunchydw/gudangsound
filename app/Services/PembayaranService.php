<?php

namespace App\Services;

use App\Models\Penyewaan;

class PembayaranService
{
    public static function tambahPembayaran(
        Penyewaan $penyewaan,
        float $nominal
    ) {

        $sisa = $penyewaan->total_harga - $penyewaan->uang_muka;

        if ($nominal > $sisa) {

            throw new \Exception(
                'Nominal melebihi sisa tagihan.'
            );
        }

        $penyewaan->uang_muka += $nominal;

        $penyewaan->status_pembayaran =
            $penyewaan->uang_muka >= $penyewaan->total_harga
            ? 'Lunas'
            : 'DP';

        $penyewaan->save();

        PemasukanService::simpan(
            $penyewaan,
            $nominal,
            $penyewaan->status_pembayaran,
            'Pembayaran lanjutan'
        );
    }
}
