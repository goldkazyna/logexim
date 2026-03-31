<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DescriptionTemplate extends Model
{
    protected $table = 'description_templates';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'template_name', 'description',
        'quantity', 'weight', 'volume_weight',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
