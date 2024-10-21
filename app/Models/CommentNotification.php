<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentNotification extends Model
{
    protected $table = 'commentnotifications';

    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'notification_id',
        'user_id',
        'comment_id',
    ];

    public $timestamps = false;

    public function taskComment()
    {
        return $this->belongsTo(TaskComment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
