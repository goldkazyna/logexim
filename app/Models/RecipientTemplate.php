<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientTemplate extends Model
{
    protected $table = 'recipient_templates';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'recipient_name', 'recipient_phone',
        'company', 'city', 'country', 'region', 'district', 'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
