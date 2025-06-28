<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use LarasoftHU\LocalizedRoutesPlus\Commands\LocalizedRoutesPlusCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RouteInstance;

class LocalizedRoutesPlusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('localized-routes-plus')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_localized_routes_plus_table')
            ->hasCommand(LocalizedRoutesPlusCommand::class);
    }

    public function register()
    {
        parent::register();
        
        // Lecseréljük a router instance-t a saját LocalizedRouter-ünkre
        $this->app->singleton('router', function ($app) {
            return new LocalizedRouter($app['events'], $app);
        });
    }

    public function boot()
    {
        parent::boot();
    }
}
