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
        'status',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPaket::class, 'paket_id');
    }
    public static function getPaketAktif()
    {
        return self::with('detail.barang')
            ->where('status', 'aktif')
            ->get()
            ->filter(function ($paket) {

                // paket tanpa barang tidak ditampilkan
                if ($paket->detail->isEmpty()) {
                    return false;
                }

                foreach ($paket->detail as $detail) {

                    if (!$detail->barang) {
                        return false;
                    }

                    if ($detail->barang->status != 'aktif') {
                        return false;
                    }
                }

                return true;
            })
            ->pluck('nama_paket', 'id');
    }
}
