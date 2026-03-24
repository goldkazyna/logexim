<?php

namespace App\Http\Controllers;

use App\Models\TmPage;

class PageController extends Controller
{
    public function show($alias)
    {
        $page = TmPage::where('alias', $alias)->firstOrFail();

        // Если есть кастомный шаблон — используем его, иначе общий
        $view = view()->exists("pages.{$alias}") ? "pages.{$alias}" : 'page';

        return view($view, compact('page'));
    }
}
