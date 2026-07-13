<?php

namespace App\Admin\Repositories;

use App\Models\Pemasukan as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Pemasukan extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
   
}
