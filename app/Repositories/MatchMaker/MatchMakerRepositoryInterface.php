<?php

namespace App\Repositories\MatchMaker;

interface MatchMakerRepositoryInterface
{
    public function get_liked_entity_count($search_id);
}