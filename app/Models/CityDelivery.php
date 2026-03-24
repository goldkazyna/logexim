<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityDelivery extends Model
{
    protected $table = 'city_delivery';
    public $timestamps = false;

    protected $fillable = ['title'];
}
