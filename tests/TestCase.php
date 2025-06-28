<?php

namespace LarasoftHU\LocalizedRoutesPlus\Tests;

use LarasoftHU\LocalizedRoutesPlus\LocalizedRoutesPlusServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LocalizedRoutesPlusServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Set up environment for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
                
        // Set up localized routes configuration
        $app['config']->set('localized-routes-plus.locales', ['en', 'hu']);
        $app['config']->set('localized-routes-plus.default_locale', 'en');
        
        // Set the application locale
        $app['config']->set('app.locale', 'en');
    }
} 