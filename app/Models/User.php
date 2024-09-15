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
        'google_id',
        'created_at',
        'updated_at',
    ];
}
