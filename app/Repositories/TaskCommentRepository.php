<?php

namespace App\Repositories;

use App\Models\TaskComment;

class TaskCommentRepository extends BaseRepository
{

    public function getModel(): string
    {
        return TaskComment::class;
    }
}
