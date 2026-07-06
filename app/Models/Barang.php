<?php

namespace App\Models;

use App\Services\InventoryService;
use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'barang';

    public function getStokHariIniAttribute()
    {
        return InventoryService::getAvailableToday($this->id);
    }

    public function getDipakaiHariIniAttribute()
    {
        return $this->jumlah_total - $this->stok_hari_ini;
    }
}
