<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $barang = [

            [
                'nama_barang' => 'Speaker Line Array',
                'kategori' => 'inti',
                'jumlah_total' => 8,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker utama'
            ],

            [
                'nama_barang' => 'Subwoofer',
                'kategori' => 'inti',
                'jumlah_total' => 6,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Mixer Digital',
                'kategori' => 'inti',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Microphone Wireless',
                'kategori' => 'pendukung',
                'jumlah_total' => 10,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Stand Speaker',
                'kategori' => 'pendukung',
                'jumlah_total' => 10,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Kabel XLR',
                'kategori' => 'pendukung',
                'jumlah_total' => 50,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Power Amplifier',
                'kategori' => 'inti',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Monitor Speaker',
                'kategori' => 'pendukung',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],

        ];

        foreach ($barang as $item) {
            Barang::create($item);
        }
    }
}
