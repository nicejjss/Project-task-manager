<?php

namespace App\Repositories;

use App\Models\Label;

class LabelRepository extends BaseRepository
{

    public function getModel(): string
    {
        return Label::class;
    }
}
