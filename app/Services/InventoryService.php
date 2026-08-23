<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Penyewaan;
use App\Models\DetailPaket;
use App\Models\Paket;

class InventoryService
{

    public static function checkAvailability(
        $tanggalMulai,
        $tanggalSelesai,
        $detailBarang = [],
        $detailPaket = [],
        $penyewaanId = null
    ) {

        $kebutuhanBarang = [];

        //1. BARANG SATUAN
        foreach ($detailBarang as $item) {

            if (
                empty($item['barang_id']) ||
                empty($item['jumlah_barang'])
            ) {
                continue;
            }

            $barang = Barang::find($item['barang_id']);

            if (!$barang) {
                throw new \Exception(
                    'Barang yang dipilih tidak ditemukan.'
                );
            }

            $jumlah = (int) $item['jumlah_barang'];

            if ($jumlah < 1) {
                throw new \Exception(
                    "Jumlah {$barang->nama_barang} minimal 1."
                );
            }

            if ($barang->status !== 'aktif') {
                throw new \Exception(
                    "Barang {$barang->nama_barang} sudah tidak aktif."
                );
            }

            if (!isset($kebutuhanBarang[$barang->id])) {
                $kebutuhanBarang[$barang->id] = 0;
            }

            $kebutuhanBarang[$barang->id] += $jumlah;
        }


        //2. PAKET

        foreach ($detailPaket as $paketItem) {

            if (
                empty($paketItem['paket_id']) ||
                empty($paketItem['jumlah_paket'])
            ) {
                continue;
            }

            $paket = Paket::find($paketItem['paket_id']);

            if (!$paket) {
                throw new \Exception(
                    'Paket yang dipilih tidak ditemukan.'
                );
            }

            if ($paket->status !== 'aktif') {
                throw new \Exception(
                    "Paket {$paket->nama_paket} sudah tidak aktif."
                );
            }

            $jumlahPaket = (int) $paketItem['jumlah_paket'];

            if ($jumlahPaket < 1) {
                throw new \Exception(
                    "Jumlah paket {$paket->nama_paket} minimal 1."
                );
            }

            $detail = DetailPaket::where(
                'paket_id',
                $paket->id
            )->get();

            if ($detail->isEmpty()) {
                throw new \Exception(
                    "Paket {$paket->nama_paket} tidak memiliki barang."
                );
            }

            //Masukkan seluruh isi paket ke kebutuhan barang.

            foreach ($detail as $barangPaket) {

                $barang = Barang::find($barangPaket->barang_id);

                if (!$barang) {
                    throw new \Exception(
                        'Terdapat barang pada paket yang sudah tidak ditemukan.'
                    );
                }

                if ($barang->status !== 'aktif') {
                    throw new \Exception(
                        "Barang {$barang->nama_barang} sudah tidak aktif " .
                            "sehingga paket tidak dapat disewakan."
                    );
                }

                $dibutuhkan =
                    (int) $barangPaket->jumlah *
                    $jumlahPaket;

                if (!isset($kebutuhanBarang[$barang->id])) {
                    $kebutuhanBarang[$barang->id] = 0;
                }

                $kebutuhanBarang[$barang->id] += $dibutuhkan;
            }
        }

        if (empty($kebutuhanBarang)) {
            throw new \Exception(
                'Minimal pilih satu paket atau satu barang.'
            );
        }

        $barangIds = array_keys($kebutuhanBarang);

        sort($barangIds);

        $barangTerkunci = Barang::whereIn('id', $barangIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');


        // Pastikan semua barang ditemukan.

        foreach ($barangIds as $barangId) {

            if (!$barangTerkunci->has($barangId)) {
                throw new \Exception(
                    'Barang yang dipilih tidak ditemukan.'
                );
            }
        }


        //CEK STOK SETELAH BARANG DI-LOCK
        foreach ($kebutuhanBarang as $barangId => $jumlahDibutuhkan) {

            $barang = $barangTerkunci->get($barangId);

            $dipakai = self::jumlahBarangDipakai(
                $barang->id,
                $tanggalMulai,
                $tanggalSelesai,
                $penyewaanId
            );

            $stokTersedia =
                (int) $barang->jumlah_total -
                (int) $dipakai;

            if ($jumlahDibutuhkan > $stokTersedia) {

                throw new \Exception(
                    "Stok {$barang->nama_barang} tidak mencukupi. " .
                        "Stok tersedia {$stokTersedia}, " .
                        "sedangkan kebutuhan {$jumlahDibutuhkan}."
                );
            }
        }
    }


    //Menghitung jumlah barang yang sedang digunakan
    // pada rentang tanggal tertentu.

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
            ->where(
                'status_penyewaan',
                '!=',
                'dibatalkan'
            )
            ->where(function ($q) use ($mulai, $selesai) {

                $q->whereBetween(
                    'tanggal_mulai',
                    [$mulai, $selesai]
                )
                    ->orWhereBetween(
                        'tanggal_selesai',
                        [$mulai, $selesai]
                    )
                    ->orWhere(function ($q) use (
                        $mulai,
                        $selesai
                    ) {

                        $q->where(
                            'tanggal_mulai',
                            '<=',
                            $mulai
                        )
                            ->where(
                                'tanggal_selesai',
                                '>=',
                                $selesai
                            );
                    });
            });

        // Jika sedang edit, penyewaan sendiri tidak dihitung.
        if ($penyewaanId) {

            $query->where(
                'id',
                '!=',
                $penyewaanId
            );
        }

        $penyewaan = $query->get();

        $total = 0;

        foreach ($penyewaan as $item) {

            // Barang satuan
            foreach ($item->detailBarang as $barang) {

                if ($barang->barang_id == $barangId) {

                    $total += (int) $barang->jumlah_barang;
                }
            }

            // Barang dari paket
            foreach ($item->detailPaket as $paket) {

                if (!$paket->paket) {
                    continue;
                }

                foreach ($paket->paket->detail as $isi) {

                    if ($isi->barang_id == $barangId) {

                        $total +=
                            (int) $isi->jumlah *
                            (int) $paket->jumlah_paket;
                    }
                }
            }
        }

        return $total;
    }


    //Stok barang yang tersedia hari ini.

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

        $tersedia =
            $barang->jumlah_total -
            $dipakai;

        return max(0, $tersedia);
    }


    //Stok tersedia pada rentang tanggal tertentu.

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

        $tersedia =
            $barang->jumlah_total -
            $dipakai;

        return max(0, $tersedia);
    }
}
