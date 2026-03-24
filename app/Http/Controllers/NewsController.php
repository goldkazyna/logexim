<?php

namespace App\Http\Controllers;

use App\Models\TmNews;

class NewsController extends Controller
{
    public function index()
    {
        $news = TmNews::orderBy('date', 'desc')->get();
        return view('news.index', compact('news'));
    }

    public function detail($id)
    {
        $news = TmNews::findOrFail($id);
        return view('news.detail', compact('news'));
    }
}
