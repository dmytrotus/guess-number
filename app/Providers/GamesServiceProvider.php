<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GamesService;

class GamesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('GamesService', GamesService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
