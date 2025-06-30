<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

it('can group routes', function () {
    // Létrehozunk egy lokalizált route-ot
    Route::group(['prefix' => 'test/group'], function () {
        Route::get('test', function () {
            return 'test';
        })->name('test')->localized();
    });

    $routes = Route::getRoutes();
    foreach ($routes as $route) {
      if ($route->getName() && str_contains($route->uri(), 'test/group')) {
          expect($route->getLocale())->not->toBe(null);
          $locale = $route->getLocale();
          if($locale != config('localized-routes-plus.default_locale')) {
            expect($route->uri())->toBe($locale.'/test/group/test');
          }
        }
    }

    // Szimuláljuk, hogy az 'en.test' route aktív
    $request = Request::create('/test', 'GET');
    $route = Route::getRoutes()->getByName('en.test');
    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    app()->instance('request', $request);

    // A route_is 'test' pattern-nek meg kell feleljen
    expect(route_is('test'))->toBeTrue();
});