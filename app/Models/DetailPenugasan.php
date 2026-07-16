<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenugasan extends Model
{
    protected $table = 'detail_penugasan';

    protected $fillable = [
        'penugasan_id',
        'user_id',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Administrator::class, 'user_id');
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class);
    }
}
