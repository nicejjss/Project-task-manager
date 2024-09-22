<?php

namespace App\Repositories;

use App\Models\UserNotification;

class UserNotificationRepository extends BaseRepository
{

    public function getModel(): string
    {
        return UserNotification::class;
    }
}
