<?php

use Illuminate\Routing\Route;
use LarasoftHU\LocalizedRoutesPlus\LocalizedRoute;

if (! function_exists('current_route')) {
    /**
     * Retrieve the current route in another locale.
     *
     * @param  string|null  $locale  The locale to use
     * @param  string|null  $country  The country to use
     * @param  array|null  $parameters  The route parameters
     * @param  bool  $absolute  Whether to generate an absolute URL
     */
    function current_route(?string $locale = null, ?string $country = null, $parameters = null, bool $absolute = true): ?string
    {
        if (! request()->route()) {
            return null;
        }

        return request()->route()->getUrl($locale, $country, $parameters, $absolute);
    }
}

if (! function_exists('route_is')) {
    /**
     * Check if the current route name matches the given pattern.
     *
     * @param  string  $name  The route name pattern to check
     */
    function route_is(string $name): bool
    {
        /** @var LocalizedRoute|null $route */
        $route = request()->route();

        if (! $route) {
            return false;
        }

        return $route != null && $route->is($name);
    }
}

if (! function_exists('localized_route')) {
    /**
     * Generate a localized route URL.
     *
     * @param  string  $name  The route name
     * @param  array  $parameters  The route parameters
     * @param  bool  $absolute  Whether to generate an absolute URL
     * @param  string|null  $locale  The locale to use
     * @param  string|null  $country  The country to use
     */
    function localized_route(string $name, $parameters = [], bool $absolute = true, ?string $locale = null, ?string $country = null)
    {
        if (config('localized-routes-plus.use_countries')) {
            if (! $locale) {
                $locale = app()->getLocale();
            }
            if (! $country) {
                $country = app()->getCountry();
            }
            $name = $locale.'-'.$country.'.'.$name;
        } else {
            if (! $locale) {
                $locale = app()->getLocale();
            }
            $name = $locale.'.'.$name;
        }

        return route($name, $parameters, $absolute);
    }
}
