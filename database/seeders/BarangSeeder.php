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
                'nama_barang' => 'Line Array RX 210',
                'kategori' => 'inti',
                'jumlah_total' => 8,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker line array'
            ],
            [
                'nama_barang' => 'Speaker Sub RT24',
                'kategori' => 'inti',
                'jumlah_total' => 18,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker subwoofer'
            ],
            [
                'nama_barang' => 'Speaker Sub 450-43',
                'kategori' => 'inti',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Speaker Sub DF1500',
                'kategori' => 'inti',
                'jumlah_total' => 40,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Power LX1000 TD',
                'kategori' => 'inti',
                'jumlah_total' => 1,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Power amplifier'
            ],
            [
                'nama_barang' => 'Power AH12004',
                'kategori' => 'inti',
                'jumlah_total' => 6,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Power amplifier'
            ],
            [
                'nama_barang' => 'Power AH10004',
                'kategori' => 'inti',
                'jumlah_total' => 1,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Power amplifier'
            ],
            [
                'nama_barang' => 'Kabel Input Line 50 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 4,
                'satuan' => 'roll',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Input Line 100 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 2,
                'satuan' => 'roll',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Line Array Tasker 4x2.5',
                'kategori' => 'pendukung',
                'jumlah_total' => 20,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Subwofer SPL 4x4',
                'kategori' => 'pendukung',
                'jumlah_total' => 10,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Strum 3x2.5 50 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 3,
                'satuan' => 'roll',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Strum 2x2.5 25 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 2,
                'satuan' => 'roll',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Strum 2x2.5 15 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 2,
                'satuan' => 'roll',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'DLMS DX2060',
                'kategori' => 'inti',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker processor'
            ],
            [
                'nama_barang' => 'DLMS DX4080',
                'kategori' => 'inti',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker processor'
            ],
            [
                'nama_barang' => 'Generator',
                'kategori' => 'pendukung',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Genset'
            ],
            [
                'nama_barang' => 'Panel Listrik',
                'kategori' => 'pendukung',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Mixer',
                'kategori' => 'inti',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Mixer audio'
            ],
            [
                'nama_barang' => 'Power Sub',
                'kategori' => 'pendukung',
                'jumlah_total' => 12,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Power Array',
                'kategori' => 'pendukung',
                'jumlah_total' => 12,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Lampu Penerangan',
                'kategori' => 'pendukung',
                'jumlah_total' => 20,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'FOH',
                'kategori' => 'pendukung',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Front of House'
            ],
            [
                'nama_barang' => 'Kabel Protektor 35 Meter',
                'kategori' => 'pendukung',
                'jumlah_total' => 4,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],

            [
                'nama_barang' => 'Kabel Mic Wisdom',
                'kategori' => 'pendukung',
                'jumlah_total' => 20,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Kabel Mic SPL',
                'kategori' => 'pendukung',
                'jumlah_total' => 10,
                'satuan' => 'pcs',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Stand Mic Almunium',
                'kategori' => 'pendukung',
                'jumlah_total' => 16,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Stand Biasa',
                'kategori' => 'inti',
                'jumlah_total' => 5,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Mic Wireless Q11',
                'kategori' => 'inti',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Wireless microphone'
            ],
            [
                'nama_barang' => 'Mic Wisdom',
                'kategori' => 'inti',
                'jumlah_total' => 10,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Mic FXT',
                'kategori' => 'inti',
                'jumlah_total' => 15,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Floor DP 15 Pro',
                'kategori' => 'inti',
                'jumlah_total' => 8,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Speaker monitor'
            ],
            [
                'nama_barang' => 'SR Monitor',
                'kategori' => 'inti',
                'jumlah_total' => 2,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'Sub Monitor',
                'kategori' => 'inti',
                'jumlah_total' => 4,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => null
            ],
            [
                'nama_barang' => 'DI',
                'kategori' => 'inti',
                'jumlah_total' => 1,
                'satuan' => 'unit',
                'status' => 'aktif',
                'keterangan' => 'Direct Injection Box'
            ],

        ];

        foreach ($barang as $item) {
            Barang::create($item);
        }
    }
}
