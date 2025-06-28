<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use LarasoftHU\LocalizedRoutesPlus\Commands\LocalizedRoutesPlusCommand;

class LocalizedRoutesPlusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('localized-routes-plus')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_localized_routes_plus_table')
            ->hasCommand(LocalizedRoutesPlusCommand::class);
    }
}
