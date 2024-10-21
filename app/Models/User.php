<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticate;

class User extends Authenticate
{
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'access_token',
        'refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'refresh_token',
        'google_id',
        'created_at',
        'updated_at',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function fileAttachments()
    {
        return $this->hasMany(FileAttachment::class, 'uploaded_by');
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    public function taskComments()
    {
        return $this->hasMany(TaskComment::class, 'user_id');
    }

    public function taskHistories()
    {
        return $this->hasMany(TaskHistory::class, 'changed_by');
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'email');
    }
}
