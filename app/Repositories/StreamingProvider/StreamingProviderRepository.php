<?php

namespace App\Repositories\StreamingProvider;

use App\Models\StreamingProvider;
use App\Repositories\StreamingProvider\StreamingProviderRepositoryInterface;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Eloquent\Repository;

class StreamingProviderRepository extends Repository implements StreamingProviderRepositoryInterface
{
    public function __construct(StreamingProvider $model)
    {
        parent::__construct($model);
    }
}