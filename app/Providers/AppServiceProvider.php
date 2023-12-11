<?php

declare(strict_types=1);

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, static fn ($app) => ClientBuilder::create()
                ->setHosts($app['config']->get('services.search.hosts'))
                ->build());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

}
