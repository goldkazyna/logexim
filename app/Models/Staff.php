<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
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
}
