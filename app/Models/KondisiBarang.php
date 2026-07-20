<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\HasDateTimeFormatter;

class KondisiBarang extends Model
{
    use HasDateTimeFormatter;

    protected $table = 'kondisi_barang';

    protected $fillable = [
        'penugasan_id',
        'barang_id',
        'jumlah_barang',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'jumlah_bermasalah',
        'catatan',
    ];

    public function penugasan()
    {
        return $this->belongsTo(
            Penugasan::class,
            'penugasan_id'
        );
    }

    public function barang()
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }
}
