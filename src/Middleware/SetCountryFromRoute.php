<?php

namespace LarasoftHU\LocalizedRoutesPlus\Middleware;

use Closure;
use Illuminate\Http\Request;
use LarasoftHU\LocalizedRoutesPlus\LocalizedRoute;

class SetCountryFromRoute
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        // Ellenőrizzük, hogy a route egy LocalizedRoute példány-e
        if ($route instanceof LocalizedRoute) {
            $country = $route->getCountry();

            if ($country) {
                app()->setCountry($country);
            }
        }

        return $next($request);
    }
}
