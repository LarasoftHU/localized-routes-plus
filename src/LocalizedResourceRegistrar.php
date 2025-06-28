<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class LocalizedResourceRegistrar extends ResourceRegistrar
{
    /**
     * Register a localized resource route.
     *
     * @param string $name
     * @param string $controller
     * @param array $options
     * @return \Illuminate\Routing\RouteCollection
     */
    public function registerLocalized(string $name, string $controller, array $options = [])
    {
        $locales = config('localized-routes-plus.locales', ['en']);

        $collection = new RouteCollection;

        foreach($locales as $locale) {
            $copy = clone $this;
            $_name = $locale . '.' . $name;
            $_options = $options;
            if(isset($_options['names'])) {
                if(is_string($options['names'])) {
                    $_options['names'] = $locale . '.' . $_options['names'];
                } else {
                    $_options['names'] = array_map(fn($name) => $locale . '.' . $name, $_options['names']);
                }
            }
            $copy->register($_name, $controller, $_options, $collection);
        }

        return $collection;
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register($name, $controller, array $options = [], ?RouteCollection $collection = null)
    {
        if (isset($options['parameters']) && ! isset($this->parameters)) {
            $this->parameters = $options['parameters'];
        }

        // If the resource name contains a slash, we will assume the developer wishes to
        // register these resource routes with a prefix so we will set that up out of
        // the box so they don't have to mess with it. Otherwise, we will continue.
        if (str_contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);

            return;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        if(!$collection) {
            $collection = new RouteCollection;
        }

        $resourceMethods = $this->getResourceMethods($defaults, $options);

        foreach ($resourceMethods as $m) {
            $optionsForMethod = $options;

            if (isset($optionsForMethod['middleware_for'][$m])) {
                $optionsForMethod['middleware'] = $optionsForMethod['middleware_for'][$m];
            }

            if (isset($optionsForMethod['excluded_middleware_for'][$m])) {
                $optionsForMethod['excluded_middleware'] = Router::uniqueMiddleware(array_merge(
                    $optionsForMethod['excluded_middleware'] ?? [],
                    $optionsForMethod['excluded_middleware_for'][$m]
                ));
            }

            $route = $this->{'addResource'.ucfirst($m)}(
                $name, $base, $controller, $optionsForMethod
            );

            if (isset($options['bindingFields'])) {
                $this->setResourceBindingFields($route, $options['bindingFields']);
            }

            if (isset($options['trashed']) &&
                in_array($m, ! empty($options['trashed']) ? $options['trashed'] : array_intersect($resourceMethods, ['show', 'edit', 'update']))) {
                $route->withTrashed();
            }

            $collection->add($route);
        }

        return $collection;
    }

}