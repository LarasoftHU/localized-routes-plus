<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Engedélyezzük a helper függvényt a teszteléshez
    include_once __DIR__.'/../../src/Helper.php';
});

it('returns false when no route is available', function () {
    // Nincs aktív route a request-ben
    expect(route_is('test.route'))->toBeFalse();
});

it('returns true when route matches the given pattern', function () {
    // Létrehozunk egy lokalizált route-ot
    Route::get('test', function () {
        return 'test';
    })->name('test')->localized();

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

it('returns false when route does not match the given pattern', function () {
    // Létrehozunk egy lokalizált route-ot
    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    // Szimuláljuk, hogy az 'en.example' route aktív
    $request = Request::create('/example', 'GET');
    $route = Route::getRoutes()->getByName('en.example');
    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    app()->instance('request', $request);

    // A route_is 'test' pattern-nek nem felel meg
    expect(route_is('test'))->toBeFalse();
});

it('handles null route gracefully', function () {
    // Szimuláljuk, hogy nincs route beállítva
    $request = Request::create('/nonexistent', 'GET');
    $request->setRouteResolver(function () {
        return null;
    });

    app()->instance('request', $request);

    expect(route_is('any.route'))->toBeFalse();
});
