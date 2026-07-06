<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Penyewaan;
use App\Models\DetailPaket;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Mengecek apakah stok masih tersedia pada rentang tanggal tertentu.
     */
    public static function checkAvailability(
        $tanggalMulai,
        $tanggalSelesai,
        $detailBarang = [],
        $detailPaket = [],
        $penyewaanId = null
    ) {
        // ==========================
        // Barang satuan
        // ==========================
        foreach ($detailBarang as $item) {

            $barang = Barang::find($item['barang_id']);

            if (!$barang) {
                continue;
            }

            $dipakai = self::jumlahBarangDipakai(
                $barang->id,
                $tanggalMulai,
                $tanggalSelesai,
                $penyewaanId
            );

            $tersisa = $barang->jumlah_total - $dipakai;

            if ($item['jumlah_barang'] > $tersisa) {

                throw new \Exception(
                    "Stok {$barang->nama_barang} tidak mencukupi. Sisa hanya {$tersisa}."
                );
            }
        }

        // ==========================
        // Paket
        // ==========================
        foreach ($detailPaket as $paketItem) {

            $detail = DetailPaket::where('paket_id', $paketItem['paket_id'])->get();

            foreach ($detail as $barangPaket) {

                $barang = Barang::find($barangPaket->barang_id);

                $dibutuhkan =
                    $barangPaket->jumlah *
                    $paketItem['jumlah_paket'];

                $dipakai = self::jumlahBarangDipakai(
                    $barang->id,
                    $tanggalMulai,
                    $tanggalSelesai,
                    $penyewaanId

                );

                $tersisa = $barang->jumlah_total - $dipakai;

                if ($dibutuhkan > $tersisa) {

                    throw new \Exception(
                        "Stok {$barang->nama_barang} tidak cukup untuk paket."
                    );
                }
            }
        }
    }

    /**
     * Menghitung jumlah barang yang sudah dipakai
     * pada rentang tanggal tertentu.
     */
    private static function jumlahBarangDipakai(
        $barangId,
        $mulai,
        $selesai,
        $penyewaanId = null
    ) {

        $query = Penyewaan::with([
            'detailBarang',
            'detailPaket.paket.detail'
        ])

            ->where('status_penyewaan', '!=', 'dibatalkan')

            ->where(function ($q) use ($mulai, $selesai) {

                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])

                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])

                    ->orWhere(function ($q) use ($mulai, $selesai) {

                        $q->where('tanggal_mulai', '<=', $mulai)

                            ->where('tanggal_selesai', '>=', $selesai);
                    });
            });

        // jika edit jangan menghitung dirinya sendiri
        if ($penyewaanId) {

            $query->where('id', '!=', $penyewaanId);
        }

        $penyewaan = $query->get();

        $total = 0;

        foreach ($penyewaan as $item) {

            /*
        ===================
        Barang satuan
        ===================
        */

            foreach ($item->detailBarang as $barang) {

                if ($barang->barang_id == $barangId) {

                    $total += $barang->jumlah_barang;
                }
            }

            /*
        ===================
        Barang dari Paket
        ===================
        */

            foreach ($item->detailPaket as $paket) {

                foreach ($paket->paket->detail as $isi) {

                    if ($isi->barang_id == $barangId) {

                        $total +=
                            $isi->jumlah *
                            $paket->jumlah_paket;
                    }
                }
            }
        }

        return $total;
    }
    public static function getAvailableToday($barangId)
    {
        $barang = Barang::find($barangId);

        if (!$barang) {
            return 0;
        }

        $hariIni = now()->toDateString();

        $dipakai = self::jumlahBarangDipakai(
            $barangId,
            $hariIni,
            $hariIni
        );

        return $barang->jumlah_total - $dipakai;
    }
    public static function getAvailableStock(
        $barangId,
        $mulai,
        $selesai
    ) {
        $barang = Barang::find($barangId);

        if (!$barang) {
            return 0;
        }

        $dipakai = self::jumlahBarangDipakai(
            $barangId,
            $mulai,
            $selesai
        );

        return $barang->jumlah_total - $dipakai;
    }
}
