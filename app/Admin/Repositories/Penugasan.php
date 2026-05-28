<?php

namespace App\Admin\Repositories;

use App\Models\Penugasan as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Penugasan extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
