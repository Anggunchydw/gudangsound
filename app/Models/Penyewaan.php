<?php

namespace App\Models;

use Carbon\Carbon;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;



class Penyewaan extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'penyewaan';

    protected $fillable = [
        'nama_penyewa',
        'no_tlp',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'keterangan',
        'total_harga',
        'uang_muka',
        'status_pembayaran',
        'status_penyewaan',
        'calendar_sync_status',
        'notification_status',
    ];

    public function detailPaket()
    {
        return $this->hasMany(DetailPenyewaanPaket::class);
    }

    public function detailBarang()
    {
        return $this->hasMany(DetailPenyewaanBarang::class);
    }

    public function getStatusSekarangAttribute()
    {
        if ($this->status_penyewaan == 'dibatalkan') {
            return 'dibatalkan';
        }

        $hariIni = Carbon::today();

        if ($hariIni->lt($this->tanggal_mulai)) {
            return 'booking';
        }

        if ($hariIni->between(
            Carbon::parse($this->tanggal_mulai),
            Carbon::parse($this->tanggal_selesai)
        )) {
            return 'berlangsung';
        }

        return 'selesai';
    }
    public function getStatusBadgeAttribute()
    {
        $status = $this->status_sekarang;

        $class = match ($status) {
            'booking' => 'status-booking',
            'berlangsung' => 'status-berlangsung',
            'selesai' => 'status-selesai',
            default => 'status-batal',
        };

        return "<span class='{$class}'>{$status}</span>";
    }
    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class);
    }
    public function penugasan()
    {
        return $this->hasOne(Penugasan::class);
    }
}
