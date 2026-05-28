<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class DetailPenyewaan extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'detail_penyewaan';
    
}
