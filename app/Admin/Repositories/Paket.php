<?php

namespace App\Admin\Repositories;

use App\Models\Paket as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Paket extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
