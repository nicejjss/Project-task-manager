<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $table = 'taskcomments';

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'task_id',
        'project_id',
        'user_id',
        'comment_text',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentNotifications()
    {
        return $this->hasMany(CommentNotification::class, 'comment_id');
    }
}
