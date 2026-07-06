<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class DetailPenyewaan extends Model
{
    // use HasDateTimeFormatter;
    // protected $table = 'detail_penyewaan';
    // protected $fillable = [
    //     'penyewaan_id',
    //     'barang_id',
    //     'paket_id',
    //     'jumlah_barang',
    // ];

    // public function barang()
    // {
    //     return $this->belongsTo(Barang::class, 'barang_id');
    // }

    // public function paket()
    // {
    //     return $this->belongsTo(Paket::class, 'paket_id');
    // }

    // public function penyewaan()
    // {
    //     return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    // }
}
