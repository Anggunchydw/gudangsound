<?php

namespace App\Admin\Repositories;

use App\Models\Penyewaan as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Penyewaan extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
