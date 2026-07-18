<?php

namespace App\Models;

use Dcat\Admin\Models\Administrator as DcatAdministrator;

class Administrator extends DcatAdministrator
{
    protected $table = 'admin_users';
    protected $fillable = [
        'username',
        'password',
        'name',
        'email',
        'avatar',
    ];
    public function detailPenugasan()
    {
        return $this->hasMany(
            DetailPenugasan::class,
            'user_id'
        );
    }
}
