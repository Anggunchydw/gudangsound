<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class DetailPaket extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'detail_paket';
    protected $primaryKey = 'id';

    protected $fillable = [
        'paket_id',
        'barang_id',
        'jumlah',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'paket_id');
    }
}
