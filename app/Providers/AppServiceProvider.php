<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//use Illuminate\Pagination\Paginator; //ToDo: Wurde für Pagination mit der Laravel hinzugefügt,  führte aber zu Problemen mit Tailwind
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Paginator::useBootstrap(); //ToDo: Wurde für Pagination mit der Laravel hinzugefügt,  führte aber zu Problemen mit Tailwind
        Schema::defaultStringLength(191);
    }
}
