<?php

namespace App\Repositories;

use App\Models\FileAttachment;

class FileAttachmentRepository extends BaseRepository
{

    public function getModel(): string
    {
        return FileAttachment::class;
    }
}
