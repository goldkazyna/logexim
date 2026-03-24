<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmPage extends Model
{
    protected $table = 'tm_pages';
    public $timestamps = false;

    protected $fillable = [
        'title', 'header', 'alias', 'mt', 'md', 'text',
        'top_menu', 'bottom_menu', 'module', 'sort',
    ];
}
