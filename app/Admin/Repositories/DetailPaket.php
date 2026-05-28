<?php

namespace App\Admin\Repositories;

use App\Models\DetailPaket as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class DetailPaket extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
