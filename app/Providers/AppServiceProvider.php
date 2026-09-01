<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Роли текущей сессии панели управления. Старые сессии, открытые до
        // многоролевости, знают только строковую role — собираем набор из неё.
        View::composer(['admin.*', 'layouts.admin'], function ($view) {
            $roles = array_values(array_filter((array) session('roles', [])));
            if ($roles === []) {
                $roles = array_values(array_filter([session('role')]));
            }
            $view->with('panelRoles', $roles);
        });

        //
    }
}
