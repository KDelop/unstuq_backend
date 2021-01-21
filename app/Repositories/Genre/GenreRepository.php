<?php

namespace App\Repositories\Genre;

use App\Models\Genre;
use App\Repositories\Genre\GenreRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class GenreRepository extends Repository implements GenreRepositoryInterface
{
    public function __construct(Genre $model)
    {
        parent::__construct($model);
    }
}