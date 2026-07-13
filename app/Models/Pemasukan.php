<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use App\Models\Penyewaan;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasDateTimeFormatter;

    protected $table = 'pemasukan';

    protected $fillable = [
        'penyewaan_id',
        'tanggal_masuk',
        'jumlah',
        'jenis_pembayaran',
        'keterangan',
    ];

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }
}
