<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\Router;
use LarasoftHU\LocalizedRoutesPlus\Middleware\SetLocaleFromRoute;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LocalizedRoutesPlusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('localized-routes-plus')
            ->hasConfigFile();
    }

    public function register()
    {
        parent::register();

        // A router-t helyesen felülírjuk a Laravel 12-ben
        $this->app->extend('router', function ($router, $app) {
            $_router = new LocalizedRouter($app['events'], $app);
            $_router->setRoutes($router->getRoutes());

            return $_router;
        });
    }

    public function boot()
    {
        parent::boot();
        
        // Regisztráljuk a middleware-t
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('set-locale-from-route', SetLocaleFromRoute::class);
    }
}
