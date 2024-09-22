<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAttachment extends Model
{
    protected $table = 'fileattachments';

    protected $primaryKey = 'file_id';

    protected $fillable = [
        'task_id',
        'file_path',
        'uploaded_by',
        'uploaded_at',
    ];

    public $timestamps = false;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
