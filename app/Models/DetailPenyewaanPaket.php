<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class DetailPenyewaanPaket extends Model
{
    protected $table = 'detail_penyewaan_paket';

    protected $fillable = [
        'penyewaan_id',
        'paket_id',
        'jumlah_paket'
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class);
    }
}
