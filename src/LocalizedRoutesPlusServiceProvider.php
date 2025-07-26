<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Foundation\Application;
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
    }

    public function boot()
    {
        parent::boot();

        Application::macro('getCountry', function () {
            return $this['config']->get('app.country', session()->has('country') ? session('country') : 'hu');
        });

        Application::macro('setCountry', function ($country) {
            $this['config']->set('app.country', $country);
            session()->put('country', $country);

            return $this; // chainable
        });
    }
}
