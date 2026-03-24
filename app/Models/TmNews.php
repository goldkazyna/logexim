<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmNews extends Model
{
    protected $table = 'tm_news';
    public $timestamps = false;

    protected $fillable = [
        'date', 'title', 'mt', 'md', 'discription', 'text',
        'img', 'thumb_571_315', 'visible',
        'title_eng', 'discription_eng', 'text_eng',
    ];
}
