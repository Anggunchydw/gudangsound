<?php

namespace App\Admin\Repositories;

use App\Models\Barang as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Barang extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
