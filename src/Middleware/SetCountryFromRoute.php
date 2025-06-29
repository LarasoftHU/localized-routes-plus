<?php

namespace LarasoftHU\LocalizedRoutesPlus\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use LarasoftHU\LocalizedRoutesPlus\LocalizedRoute;

class SetCountryFromRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        
        // Ellenőrizzük, hogy a route egy LocalizedRoute példány-e
        if ($route instanceof LocalizedRoute) {
            $country = $route->getCountry();
            
            if ($country) {
                App::setCountry($country);
            }
        }
        
        return $next($request);
    }
} 