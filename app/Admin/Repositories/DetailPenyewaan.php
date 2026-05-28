<?php

namespace App\Admin\Repositories;

use App\Models\DetailPenyewaan as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class DetailPenyewaan extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
