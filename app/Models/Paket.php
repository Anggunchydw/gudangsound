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
    public static function getPaketAktif()
    {
        return self::with('detail.barang')
            ->get()
            ->filter(function ($paket) {
                return $paket->is_active;
            })
            ->pluck('nama_paket', 'id');
    }
    public function getIsActiveAttribute()
    {
        foreach ($this->detail as $detail) {

            if (!$detail->barang) {
                return false;
            }

            if ($detail->barang->status != 'aktif') {
                return false;
            }
        }

        return true;
    }
}
