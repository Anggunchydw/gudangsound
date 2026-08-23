<?php

namespace App\Services;

use App\Models\Penyewaan;
use Illuminate\Support\Facades\DB;

class PembayaranService
{
    public static function tambahPembayaran(
        Penyewaan $penyewaan,
        float $nominal
    ) {
        return DB::transaction(function () use (
            $penyewaan,
            $nominal
        ) {

            $penyewaan = Penyewaan::whereKey(
                $penyewaan->id
            )
                ->lockForUpdate()
                ->firstOrFail();

            $sisa =
                $penyewaan->total_harga -
                $penyewaan->uang_muka;

            if ($nominal <= 0) {
                throw new \Exception(
                    'Nominal pembayaran harus lebih dari 0.'
                );
            }

            if ($nominal > $sisa) {
                throw new \Exception(
                    'Nominal melebihi sisa tagihan.'
                );
            }

            $penyewaan->uang_muka += $nominal;

            $penyewaan->status_pembayaran =
                $penyewaan->uang_muka >=
                $penyewaan->total_harga
                ? 'Lunas'
                : 'DP';

            $penyewaan->save();

            PemasukanService::simpan(
                $penyewaan,
                $nominal,
                $penyewaan->status_pembayaran,
                'Pembayaran lanjutan'
            );

            return $penyewaan;
        });
    }
}
