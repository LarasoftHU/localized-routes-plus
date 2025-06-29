<?php

use LarasoftHU\LocalizedRoutesPlus\LocalizedRoute;

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

        return $route !== null && $route->is($name);
    }
}
