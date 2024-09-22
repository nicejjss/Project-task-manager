<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $table = 'taskhistory';

    protected $primaryKey = 'history_id';

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'task_id',
        'changed_by',
        'change_description',
        'change_type',
        'created_at',
    ];

    public $timestamps = false;

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
