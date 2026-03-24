<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmImage extends Model
{
    protected $table = 'tm_images';
    public $timestamps = false;

    protected $fillable = ['img', 'title', 'date'];
}
