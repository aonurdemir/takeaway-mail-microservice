<?php

namespace App\Providers;

use App\Repositories\EloquentMailRepository;
use App\Repositories\MailRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            MailRepository::class,
            function ($app) {
                return new EloquentMailRepository();
            }
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
