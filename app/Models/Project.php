<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $primaryKey = 'project_id';

    //TODO: add cloded_plan_at
    protected $fillable = [
        'owner_id',
        'project_name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'closed_plan_at',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function taskTypes()
    {
        return $this->hasMany(TaskType::class, 'project_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }
}
