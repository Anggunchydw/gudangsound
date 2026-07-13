<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Penyewaan;

class PemasukanService
{
    public static function simpan(
        Penyewaan $penyewaan,
        $jumlah,
        $jenis,
        $keterangan = null
    ) {
        Pemasukan::create([
            'penyewaan_id'     => $penyewaan->id,
            'tanggal_masuk'    => now(),
            'jumlah'           => $jumlah,
            'jenis_pembayaran' => $jenis,
            'keterangan'       => $keterangan,
        ]);
    }
}
