<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class LocalizedResourceRegistrar extends ResourceRegistrar
{
    public $locale;

    public $locales = [];

    public $uriLocalized = false;

    /**
     * Register a localized resource route.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function registerLocalized(string $name, string $controller, array $options = [], array $locales = [], bool $uriLocalized = false)
    {
        if (! empty($locales)) {
            $collection = new RouteCollection;
            $this->uriLocalized = $uriLocalized;

            // Check if this is a prefixed resource (contains slash) AND using subdomains
            if (str_contains($name, '/') && config('localized-routes-plus.use_subdomains_instead_of_prefixes')) {
                // For prefixed resources with subdomains, we need to handle all locales at once
                $this->locales = $locales;
                $this->register($name, $controller, $options);
                
                // Return empty collection as routes are registered directly to router
                return $collection;
            } else {
                foreach ($locales as $locale) {
                    $copy = clone $this;
                    $copy->locale = $locale;
                    
                    // For regular resources and prefix mode, process each locale separately
                    if (str_contains($name, '/')) {
                        // For prefixed resources, routes are registered directly to the router
                        $copy->register($name, $controller, $options);
                    } else {
                        // For regular resources, we can get the collection back
                        $localeCollection = $copy->register($name, $controller, $options);
                        
                        if ($localeCollection) {
                            // Add all routes from the locale-specific collection to the main collection
                            foreach ($localeCollection->getRoutes() as $route) {
                                $collection->add($route);
                            }
                        }
                    }
                }
                
                return $collection;
            }
        } else {
            return $this->register($name, $controller, $options);
        }
    }

    /**
     * Get the base resource URI for a given resource.
     *
     * @param  string  $resource
     * @return string
     */
    public function getResourceUri($resource)
    {
        if ($this->doInjectLocaleForUri()) {
            return '/'.$this->locale.'/'.$this->getResourceUriWithoutLocale($resource);
        } else {
            return $this->getResourceUriWithoutLocale($resource);
        }
    }

    private function getResourceUriWithoutLocale($resource)
    {
        if (! str_contains($resource, '.')) {
            return $resource;
        }

        // Once we have built the base URI, we'll remove the parameter holder for this
        // base resource name so that the individual route adders can suffix these
        // paths however they need to, as some do not have any parameters at all.
        $segments = explode('.', $resource);

        $uri = $this->getNestedResourceUri($segments);

        return str_replace('/{'.$this->getResourceWildcard(end($segments)).'}', '', $uri);

    }

    /**
     * Get the name for a given resource.
     *
     * @param  string  $resource
     * @param  string  $method
     * @param  array  $options
     * @return string
     */
    protected function getResourceRouteName($resource, $method, $options)
    {
        if ($this->locale) {
            return $this->locale.'.'.$this->getResourceRouteNameWithoutLocale($resource, $method, $options);
        } else {
            return $this->getResourceRouteNameWithoutLocale($resource, $method, $options);
        }
    }

    /**
     * Get the name for a given resource.
     *
     * @param  string  $resource
     * @param  string  $method
     * @param  array  $options
     * @return string
     */
    protected function getResourceRouteNameWithoutLocale($resource, $method, $options)
    {
        $name = $resource;

        // If the names array has been provided to us we will check for an entry in the
        // array first. We will also check for the specific method within this array
        // so the names may be specified on a more "granular" level using methods.
        if (isset($options['names'])) {
            if (is_string($options['names'])) {
                $name = $options['names'];
            } elseif (isset($options['names'][$method])) {
                return $options['names'][$method];
            }
        }

        // If a global prefix has been assigned to all names for this resource, we will
        // grab that so we can prepend it onto the name when we create this name for
        // the resource action. Otherwise we'll just use an empty string for here.
        $prefix = isset($options['as']) ? $options['as'].'.' : '';

        return trim(sprintf('%s%s.%s', $prefix, $name, $method), '.');
    }

    /**
     * Build a set of prefixed resource routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return \Illuminate\Routing\Router
     */
    protected function prefixedResource($name, $controller, array $options)
    {
        [$name, $prefix] = $this->getResourcePrefix($name);

        if ($this->doInjectLocaleForUri()) {
            $prefix = '/'.$this->locale.'/'.$prefix;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $callback = function ($me) use ($name, $controller, $options) {
            $resource = $me->resource($name, $controller, $options);
            
            // If we have multiple locales to process (from registerLocalized) and using subdomains
            if (!empty($this->locales) && config('localized-routes-plus.use_subdomains_instead_of_prefixes')) {
                $resource->localized($this->locales);
            } elseif ($this->locale) {
                // For subdomain mode, we don't need uriLocalized since domains handle localization
                if (config('localized-routes-plus.use_subdomains_instead_of_prefixes')) {
                    $resource->localized();
                } else {
                    $resource->localized($this->locale)->uriLocalized();
                }
            }
        };

        return $this->router->group(compact('prefix'), $callback);
    }

    private function doInjectLocaleForUri(): bool
    {
        if(config('localized-routes-plus.use_subdomains_instead_of_prefixes')){
            return false;
        }

        if($this->uriLocalized){
            return false;
        }

        if (! empty($this->locale)) {
            if ($this->locale === config('localized-routes-plus.default_locale') && ! config('localized-routes-plus.use_route_prefix_in_default_locale')) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
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

            return new RouteCollection;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        if (! $collection) {
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

            if ($this->locale) {
                $route->setLocale($this->locale);
            }

            if (isset($options['bindingFields'])) {
                $this->setResourceBindingFields($route, $options['bindingFields']);
            }

            if (isset($options['trashed']) &&
                in_array($m, ! empty($options['trashed']) ? $options['trashed'] : array_intersect($resourceMethods, ['show', 'edit', 'update']))) {
                $route->withTrashed();
            }
            //$route->domain('apple.hu');
            $route = $this->processDomainForRoute($route);

            $collection->add($route);
        }

        return $collection;
    }

    private function processDomainForRoute(Route $route) : Route
    {
        if (config('localized-routes-plus.use_subdomains_instead_of_prefixes') == true) {
            if (isset(config('localized-routes-plus.domains')[$this->locale])) {

                if(is_array(config('localized-routes-plus.domains')[$this->locale])){
                    if(count(config('localized-routes-plus.domains')[$this->locale]) > 0){
                        $originalName = $route->action['as'];
                        $domains = config('localized-routes-plus.domains')[$this->locale];
                        $route = $route->domain($domains[0]);
                        $route->action['as'] = explode('.', $domains[0])[0].'-'.$originalName;
                        for ($i = 1, $count = count($domains); $i < $count; $i++) {
                            $copy = clone $route;
                            $copy->domain($domains[$i]);
                            $copy->action['as'] = explode('.', $domains[$i])[0].'-'.$originalName;
                            $this->router->getRoutes()->add($copy);
                        }
                    }
                }else {
                    $domain = config('localized-routes-plus.domains')[$this->locale];
                    $route = $route->domain(config('localized-routes-plus.domains')[$this->locale]);
                }
            } else {
                throw new \InvalidArgumentException('Domain not found for locale: '.$this->locale. ' If you want to exclude this locale, you can use the localizedExcept(\''.$this->locale.'\') method.');
            }
        }

        return $route;
    }
}
