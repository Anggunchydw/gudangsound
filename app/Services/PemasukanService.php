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

        // Jika tidak dikirim, tentukan otomatis
        if (!$keterangan) {
            $keterangan = $jenis == 'DP'
                ? 'Pembayaran DP'
                : 'Pelunasan';
        }

        $pemasukan = Pemasukan::where('penyewaan_id', $penyewaan->id)
            ->where('jenis_pembayaran', $jenis)
            ->first();

        if (!$pemasukan) {

            Pemasukan::create([
                'penyewaan_id'     => $penyewaan->id,
                'tanggal_masuk'    => now(),
                'jumlah'           => $jumlah,
                'jenis_pembayaran' => $jenis,
                'keterangan'       => $keterangan,
            ]);

        } else {

            $pemasukan->update([
                'jumlah'      => $jumlah,
                'keterangan'  => $keterangan,
            ]);
        }
    }
}
