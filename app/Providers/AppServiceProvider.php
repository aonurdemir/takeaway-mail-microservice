<?php

namespace App\Providers;

use App\Repositories\EloquentMailJobRepository;
use App\Repositories\MailJobRepository;
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
        $this->app->singleton(MailJobRepository::class, function ($app) {
            return new EloquentMailJobRepository();
        });
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
