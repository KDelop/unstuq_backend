<?php

namespace App\Repositories\DateIdea;

use App\Models\DateIdea;

interface DateIdeaRepositoryInterface
{
    public function get_date_ideas($userId);
}
