<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class LocalizedResourceRegistrar extends ResourceRegistrar
{
    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register($name, $controller, array $options = [], $locales = [])
    {
        if (isset($options['parameters']) && ! isset($this->parameters)) {
            $this->parameters = $options['parameters'];
        }
        // If the resource name contains a slash, we will assume the developer wishes to
        // register these resource routes with a prefix so we will set that up out of
        // the box so they don't have to mess with it. Otherwise, we will continue.

        /*
        //TODO:PREFIX NOT USPPORTED JET
        if (str_contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options, $locales);
            return;
        }
        */

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        $collection = new RouteCollection;

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

            if ($locales && count($locales) > 0) {
                $route = $route->localized($locales);
            }

            $collection->add($route);
        }

        return $collection;
    }

    /**
     * Build a set of prefixed resource routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return \Illuminate\Routing\Router
     */
    protected function prefixedResource($name, $controller, array $options, array $locales = [])
    {
        [$name, $prefix] = $this->getResourcePrefix($name);

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $callback = function ($me) use ($name, $controller, $options, $locales) {
            $resource = $me->resource($name, $controller, $options);
            if ($locales && count($locales) > 0) {
                $resource->localized($locales);
            }
        };

        return $this->router->group(compact('prefix'), $callback);
    }
}
