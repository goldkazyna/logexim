<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'staff';

    protected $fillable = [
        'full_name',
        'login',
        'password',
        'role',
        'phone',
        'email',
        'note',
        'active',
    ];

    protected $hidden = [
        'password',
    ];
}
