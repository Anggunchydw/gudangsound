<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paket;
use App\Models\DetailPaket;

class PaketSeeder extends Seeder
{
    public function run()
    {

        $paket1 = Paket::create([
            'nama_paket' => 'Paket Wedding / Hajatan',
            'deskripsi' => 'Paket sound system untuk acara wedding dan hajatan.'
        ]);

        DetailPaket::insert([
            [
                'paket_id' => $paket1->id,
                'barang_id' => 1,
                'jumlah' => 8
            ],
            [
                'paket_id' => $paket1->id,
                'barang_id' => 2,
                'jumlah' => 8
            ],
            [
                'paket_id' => $paket1->id,
                'barang_id' => 31,
                'jumlah' => 4
            ],
            [
                'paket_id' => $paket1->id,
                'barang_id' => 32,
                'jumlah' => 2
            ],
            [
                'paket_id' => $paket1->id,
                'barang_id' => 22,
                'jumlah' => 8
            ],
        ]);


        $paket2 = Paket::create([
            'nama_paket' => 'Paket Pengajian',
            'deskripsi' => 'Paket sound system untuk acara pengajian.'
        ]);

        DetailPaket::insert([
            [
                'paket_id' => $paket2->id,
                'barang_id' => 1,
                'jumlah' => 4
            ],
            [
                'paket_id' => $paket2->id,
                'barang_id' => 2,
                'jumlah' => 4
            ],
            [
                'paket_id' => $paket2->id,
                'barang_id' => 31,
                'jumlah' => 2
            ],
            [
                'paket_id' => $paket2->id,
                'barang_id' => 22, 
                'jumlah' => 4
            ],
        ]);
    }
}
