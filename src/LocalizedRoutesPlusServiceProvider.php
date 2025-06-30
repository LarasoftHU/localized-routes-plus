<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Foundation\Application;
use Illuminate\Routing\CompiledRouteCollection;
use Illuminate\Routing\Router;
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
        // A router-t helyesen felülírjuk a Laravel 12-ben
        $this->app->extend('router', function ($router, $app) {
            $_router = new LocalizedRouter($app['events'], $app);

            foreach (get_object_vars($router) as $key => $value) {
                $_router->$key = $value;
            }

            $routes = $router->getRoutes();
            $_router->setRoutes($routes);

            return $_router;
        });
        parent::register();
    }
    

    public function boot()
    {
        parent::boot();

        Application::macro('getCountry', function () {
            return $this['config']->get('app.country', session()->has('country') ? session('country') : 'HU');
        });

        Application::macro('setCountry', function ($country) {
            $this['config']->set('app.country', $country);
            session()->put('country', $country);

            return $this; // chainable
        });
    }
}
