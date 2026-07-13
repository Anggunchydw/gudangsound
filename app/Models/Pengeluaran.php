<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'pengeluaran';
    protected $fillable = [
    'penyewaan_id',
    'jumlah_pengeluaran',
    'tanggal_pengeluaran',
    'kategori',
    'keterangan',
];

public function penyewaan()
{
    return $this->belongsTo(Penyewaan::class);
}
}
