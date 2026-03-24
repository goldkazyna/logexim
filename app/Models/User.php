<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'bin', 'password', 'ip', 'date', 'activate', 'activate_code',
        'company_name', 'phone', 'email', 'director_name',
        'address', 'city', 'region', 'country', 'district', 'restore_code',
    ];

    protected $hidden = [
        'password', 'activate_code', 'restore_code',
    ];

    public function recipientTemplates()
    {
        return $this->hasMany(RecipientTemplate::class);
    }
}
