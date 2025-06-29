<?php

namespace LarasoftHU\LocalizedRoutesPlus\Middleware;

use Closure;

class SetCountryFromSession
{
    public function handle($request, Closure $next)
    {
        if (session()->has('country')) {
            app()->setCountry(session('country'));
        }

        return $next($request);
    }
}
