<?php

namespace App\Repositories;

use App\Models\TaskType;

class TaskTypeRepository extends BaseRepository
{

    public function getModel(): string
    {
        return TaskType::class;
    }
}
