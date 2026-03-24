<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avia extends Model
{
    protected $table = 'avia';
    public $timestamps = false;

    protected $fillable = ['city_from', 'city_to', 'price', 'time'];

    public function cityFrom()
    {
        return $this->belongsTo(CityDelivery::class, 'city_from');
    }

    public function cityTo()
    {
        return $this->belongsTo(CityDelivery::class, 'city_to');
    }
}
