<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
//    public function all(): Collection;
    public function user_exists_check($data);
    public function get_favorites($user_id, $type);
    public function get_feedbacks($user_id);
    public function get_devices($user_id);

}