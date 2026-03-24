<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmOrder extends Model
{
    protected $table = 'tm_order';
    public $timestamps = false;

    protected $fillable = [
        'track_number', 'name_from', 'name_to',
        'date_from', 'date_to', 'status',
    ];
}
