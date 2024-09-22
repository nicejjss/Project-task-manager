<?php

namespace App\Repositories;

use App\Models\TaskHistory;

class TaskHistoryRepository extends BaseRepository
{

    public function getModel(): string
    {
        return TaskHistory::class;
    }
}
