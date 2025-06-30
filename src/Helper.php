<?php

use Illuminate\Routing\Route;
use LarasoftHU\LocalizedRoutesPlus\LocalizedRoute;

if (! function_exists('current_route')) {
    /**
     * Retrieve the current route in another locale.
     *
     * @param  string|null  $fallback
     * @param  bool  $absolute
     */
    function current_route(?string $locale = null, ?string $country = null): ?string
    {
        if(!request()->route()) return null;

        return request()->route()->getUrl($locale, $country);
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

        if(!$route) return false;

        return $route !== null && $route->is($name);
    }
}

if (! function_exists('localized_route')) {
    function localized_route(string $name, $parameters = [], bool $absolute = true, ?bool $locale = null, ?bool $country = null)
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
