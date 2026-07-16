<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    protected $table = 'penugasan';

    protected $fillable = [
        'penyewaan_id',
        'tim',
    ];

    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }

    public function anggota()
    {
        return $this->hasMany(
            DetailPenugasan::class,
            'penugasan_id'
        );
    }

    public function pegawai()
    {
        return $this->belongsToMany(
            Administrator::class,
            'detail_penugasan',
            'penugasan_id',
            'user_id'
        );
    }
}
