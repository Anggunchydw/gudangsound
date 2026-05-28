<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'paket';

    protected $fillable = [
        'nama_paket',
        'deskripsi',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPaket::class, 'paket_id');
    }
}
