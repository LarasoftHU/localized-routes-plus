<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\RoutingServiceProvider;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

class LocalizedRoutingServiceProvider extends RoutingServiceProvider
{
    private function copyAllProperties(object $source, object $target): void
    {
        $sourceRef = new ReflectionObject($source);
        $targetRef = new ReflectionObject($target);

        // Végigmegyünk az összes property-n (minden láthatóság)
        foreach ($sourceRef->getProperties() as $sourceProp) {
            $sourceProp->setAccessible(true);
            $value = $sourceProp->getValue($source);

            // Próbáljuk meg beállítani a target objektum megfelelő property-jét
            // Ha nincs targetben ilyen property, megpróbáljuk létrehozni dinamikusan (ha lehetséges)
            try {
                $targetProp = $targetRef->getProperty($sourceProp->getName());
                $targetProp->setAccessible(true);
                $targetProp->setValue($target, $value);
            } catch (ReflectionException $e) {
                // Ha nincs ilyen property a target osztályban,
                // és a target dinamikusan engedi a tulajdonságok hozzáadását (pl. nincs __set korlátozás),
                // akkor beállítjuk dinamikusan
                $target->{$sourceProp->getName()} = $value;
            }
        }
    }

    /**
     * Register the router instance.
     */
    protected function registerRouter()
    {
        // A router-t helyesen felülírjuk a Laravel 12-ben

        $this->app->extend('router', function ($router, $app) {
            $_router = new LocalizedRouter($app['events'], $app);

            foreach (get_object_vars($router) as $key => $value) {
                $_router->$key = $value;
            }

            $this->copyAllProperties($router, $_router);
            $_router->setRoutes($router->getRoutes());

            return $_router;
        });

        $this->app->extend(\Illuminate\Contracts\Http\Kernel::class, function ($kernel, $app) {
            $reflection = new ReflectionClass($kernel);
            $property = $reflection->getProperty('router');
            $property->setAccessible(true); // protected/private property

            $property->setValue($kernel, $app['router']);

            return $kernel;
        });

    }
}
