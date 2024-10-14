<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'task_id',
        'project_id',
        'label_id',
        'created_by',
        'assigned_to',
        'title',
        'description',
        'priority',
        'deadline',
        'status',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'tasktype_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(FileAttachment::class, 'task_id');
    }
}
