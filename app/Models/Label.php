<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';

    protected $primaryKey = 'label_id';

    protected $fillable = [
        'project_id',
        'label_name',
        'color_code',
    ];

    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'label_id');
    }
}
