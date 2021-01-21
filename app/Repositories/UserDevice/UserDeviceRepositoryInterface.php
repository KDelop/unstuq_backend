<?php

namespace App\Repositories\UserDevice;

use App\Models\UserDevice;

interface UserDeviceRepositoryInterface
{
    public function check_device_exists($data);

}