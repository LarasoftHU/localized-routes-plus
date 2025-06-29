<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use LarasoftHU\LocalizedRoutesPlus\Commands\LocalizedRoutesPlusCommand;
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
            //return new LocalizedRouter($app['events'], $app);
            return $router;
        });
    }

    public function boot()
    {
        parent::boot();
    }
}
