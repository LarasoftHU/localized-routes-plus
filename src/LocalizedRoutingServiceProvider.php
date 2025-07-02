<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\RoutingServiceProvider;

class LocalizedRoutingServiceProvider extends RoutingServiceProvider
{
    /**
     * Register the router instance.
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new LocalizedRouter($app['events'], $app);
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        // Újra regisztráljuk a routert a testreszabott verziónkkal
        $this->registerRouter();
    }
}
