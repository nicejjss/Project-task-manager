<?php

namespace App\Repositories;

use App\Models\CommentNotification;

class CommentNotificationRepository extends BaseRepository
{

    public function getModel(): string
    {
        return CommentNotification::class;
    }
}
