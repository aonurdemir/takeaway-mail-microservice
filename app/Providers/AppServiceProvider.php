<?php

namespace App\Providers;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Builder;
use Ackintosh\Ganesha\Storage\Adapter\Redis as GaneshaRedisAdapter;
use App\Repositories\EloquentMailRepository;
use App\Repositories\MailRepository;
use App\Services\MailjetMailProvider;
use App\Services\MailProvider;
use App\Services\SendGridMailProvider;
use App\Services\Utils\MailProviderIterator;
use Illuminate\Support\ServiceProvider;
use Mailjet\Client;
use Redis;
use SendGrid;

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

        $this->app->singleton(
            SendGrid::class,
            function ($app) {
                return new SendGrid(config('services.sendgrid.api_key'));
            }
        );

        $this->app->singleton(
            Client::class,
            function ($app) {
                return new Client(
                    config('services.mailjet.key'),
                    config('services.mailjet.secret'),
                    true,
                    ['version' => 'v3.1']
                );
            }
        );

        $this->app->singleton(
            Ganesha::class,
            function ($app) {
                return Builder::withRateStrategy()
                              ->adapter(new GaneshaRedisAdapter($app->make(Redis::class)))
                              ->failureRateThreshold(config('circuit_breaker.failure_rate_threshold'))
                              ->intervalToHalfOpen(config('circuit_breaker.interval_to_half_open'))
                              ->minimumRequests(config('circuit_breaker.minimum_requests'))
                              ->timeWindow(config('circuit_breaker.time_window'))
                              ->build();
            }
        );

        $this->app->when(MailProviderIterator::class)
                  ->needs(MailProvider::class)
                  ->give(
                      function ($app) {
                          return [
                              $app->make(SendGridMailProvider::class),
                              $app->make(MailjetMailProvider::class),
                          ];
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
