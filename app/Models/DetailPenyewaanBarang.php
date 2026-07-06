<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class DetailPenyewaanBarang extends Model
{
    protected $table = 'detail_penyewaan_barang';

    protected $fillable = [
        'penyewaan_id',
        'barang_id',
        'jumlah_barang'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class);
    }
}
