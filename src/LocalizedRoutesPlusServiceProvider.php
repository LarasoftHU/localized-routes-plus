<?php

namespace LarasoftHU\LocalizedRoutesPlus;

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

        // Lecseréljük a router instance-t a saját LocalizedRouter-ünkre
        $this->app->singleton('router', function ($app) {
            return new LocalizedRouter($app['events'], $app);
        });

        Route::setFacadeApplication($this->app);
    }

    public function boot()
    {
        parent::boot();
    }
}
