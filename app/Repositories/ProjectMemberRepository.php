<?php

namespace App\Repositories;

use App\Models\ProjectMember;

class ProjectMemberRepository extends BaseRepository
{

    public function getModel(): string
    {
        return ProjectMember::class;
    }
}
