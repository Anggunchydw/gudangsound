<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paket;
use App\Models\DetailPaket;

class PaketSeeder extends Seeder
{
    public function run()
    {

        $paket = Paket::create([
            'nama_paket'=>'Paket Hajatan',
            'deskripsi'=>'Untuk acara hajatan'
        ]);

        DetailPaket::insert([

            [
                'paket_id'=>$paket->id,
                'barang_id'=>1,
                'jumlah'=>2
            ],

            [
                'paket_id'=>$paket->id,
                'barang_id'=>2,
                'jumlah'=>2
            ],

            [
                'paket_id'=>$paket->id,
                'barang_id'=>4,
                'jumlah'=>2
            ],

        ]);

        $paket2=Paket::create([

            'nama_paket'=>'Paket Pengajian',
            'deskripsi'=>'Untuk pengajian'

        ]);

        DetailPaket::insert([

            [
                'paket_id'=>$paket2->id,
                'barang_id'=>1,
                'jumlah'=>4
            ],

            [
                'paket_id'=>$paket2->id,
                'barang_id'=>2,
                'jumlah'=>4
            ],

            [
                'paket_id'=>$paket2->id,
                'barang_id'=>5,
                'jumlah'=>4
            ],

        ]);

    }
}
