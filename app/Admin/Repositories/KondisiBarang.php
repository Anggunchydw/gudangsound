<?php

namespace App\Admin\Repositories;

use App\Models\KondisiBarang as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class KondisiBarang extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
