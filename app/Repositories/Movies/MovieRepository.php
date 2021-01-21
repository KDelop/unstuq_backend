<?php

namespace App\Repositories\Movies;

use App\Models\movie as movies;
use App\Repositories\Movies\MovieRepositoryInterface;

use App\Repositories\Business\BusinessRepositoryInterface;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class MovieRepository extends Repository implements MovieRepositoryInterface
{
    public function __construct(movies $model)
    {
        parent::__construct($model);
    }
}
