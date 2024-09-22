<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $table = 'projectmembers';

    const CREATED_AT = 'created_at';

    protected $primaryKey = 'project_member_id';

    protected $fillable = [
        'project_id',
        'user_id',
        'joined_at',
    ];


    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
