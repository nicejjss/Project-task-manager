<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $table = 'tasktypes';

    protected $primaryKey = 'tasktype_id';

    protected $fillable = [
        'project_id',
        'tasktype_name',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'tasktype_id');
    }
}
