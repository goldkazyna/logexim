<?php

namespace App\Http\Controllers;

use App\Models\TmNews;

class HomeController extends Controller
{
    public function index()
    {
        $news = TmNews::orderBy('date', 'desc')->limit(3)->get();
        return view('home', compact('news'));
    }
}
