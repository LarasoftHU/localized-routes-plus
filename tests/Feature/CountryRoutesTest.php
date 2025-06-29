<?php

use Illuminate\Support\Facades\Route;

// Alap országos útvonal tesztek

it('creates routes with country codes when use_countries is enabled', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);
    config()->set('localized-routes-plus.country_path_separator', 'dash');

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en-us.example route
    expect($routes->hasNamedRoute('en-us.example'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu-hu.example route
    expect($routes->hasNamedRoute('hu-hu.example'))->toBeTrue();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    expect($enRoute->getLocale())->toBe('en');
    expect($enRoute->getCountry())->toBe('us');
    expect($huRoute->getLocale())->toBe('hu');
    expect($huRoute->getCountry())->toBe('hu');
});

it('creates routes with dash separator in URL when country_path_separator is dash', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);
    config()->set('localized-routes-plus.country_path_separator', 'dash');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', true);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    expect($enRoute->uri())->toBe('en-us/example');
    expect($huRoute->uri())->toBe('hu-hu/example');
});

it('creates routes with slash separator in URL when country_path_separator is slash', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);
    config()->set('localized-routes-plus.country_path_separator', 'slash');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', true);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    expect($enRoute->uri())->toBe('en/us/example');
    expect($huRoute->uri())->toBe('hu/hu/example');
});

it('creates multiple routes when countries is an array', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en']);
    config()->set('localized-routes-plus.countries', [
        'en' => ['us', 'ca', 'gb'],
    ]);
    config()->set('localized-routes-plus.country_path_separator', 'dash');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', true);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy mindhárom országhoz létrejött route
    expect($routes->hasNamedRoute('en-us.example'))->toBeTrue();
    expect($routes->hasNamedRoute('en-ca.example'))->toBeTrue();
    expect($routes->hasNamedRoute('en-gb.example'))->toBeTrue();

    $usRoute = $routes->getByName('en-us.example');
    $caRoute = $routes->getByName('en-ca.example');
    $gbRoute = $routes->getByName('en-gb.example');

    expect($usRoute->getCountry())->toBe('us');
    expect($caRoute->getCountry())->toBe('ca');
    expect($gbRoute->getCountry())->toBe('gb');

    expect($usRoute->uri())->toBe('en-us/example');
    expect($caRoute->uri())->toBe('en-ca/example');
    expect($gbRoute->uri())->toBe('en-gb/example');
});

it('works with default locale prefix disabled', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);
    config()->set('localized-routes-plus.country_path_separator', 'dash');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    // Default locale nem tartalmazza a prefixet
    expect($enRoute->uri())->toBe('example');
    // Nem default locale tartalmazza
    expect($huRoute->uri())->toBe('hu-hu/example');
});

// Locale metódus tesztek országokkal

it('locale method works with countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    /** @var \LarasoftHU\LocalizedRoutesPlus\LocalizedRoute $enRoute */
    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $enRoute->locale('hu', 'hu');

    expect($huRoute->getName())->toBe('hu-hu.example');
    expect($huRoute->getLocale())->toBe('hu');
    expect($huRoute->getCountry())->toBe('hu');
});

it('locale method throws exception when country parameter is missing', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    /** @var \LarasoftHU\LocalizedRoutesPlus\LocalizedRoute $enRoute */
    $enRoute = $routes->getByName('en-us.example');

    expect(fn () => $enRoute->locale('hu'))
        ->toThrow(InvalidArgumentException::class, 'You can not use locale() method without country parameter if use_countries config is true!');
});

it('locale method throws exception when use_countries is false but country is provided', function () {
    config()->set('localized-routes-plus.use_countries', false);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    /** @var \LarasoftHU\LocalizedRoutesPlus\LocalizedRoute $enRoute */
    $enRoute = $routes->getByName('en.example');

    expect(fn () => $enRoute->locale('hu', 'hu'))
        ->toThrow(InvalidArgumentException::class, 'You can not use country parameter without use_countries config!');
});

// getUrl metódus tesztek országokkal

it('getUrl method works with countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $url = $enRoute->getUrl('hu', 'hu');

    expect($url)->toContain('hu-hu/example');
});

it('getUrl method throws exception when country parameter is missing with use_countries enabled', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');

    expect(fn () => $enRoute->getUrl('hu'))
        ->toThrow(InvalidArgumentException::class, 'You can not use getUrl() method without country parameter if use_countries config is true!');
});

it('getUrl method throws exception when country parameter is provided but use_countries is disabled', function () {
    config()->set('localized-routes-plus.use_countries', false);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en.example');

    expect(fn () => $enRoute->getUrl('hu', 'hu'))
        ->toThrow(InvalidArgumentException::class, 'You can not use getUrl() method with country parameter if use_countries config is false!');
});

// getSafeName metódus tesztek országokkal

it('getSafeName method works correctly with countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    expect($enRoute->getSafeName())->toBe('example');
    expect($huRoute->getSafeName())->toBe('example');
});

// Resource route tesztek országokkal

it('resource routes work with countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::resource('posts', 'PostController')->localized()->names('posts');

    $routes = Route::getRoutes();

    // Ellenőrizzük, hogy minden resource route létrejött mindkét országhoz
    $resourceActions = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    foreach ($resourceActions as $action) {
        expect($routes->hasNamedRoute("en-us.posts.$action"))->toBeTrue();
        expect($routes->hasNamedRoute("hu-hu.posts.$action"))->toBeTrue();
    }
});

// POST, PUT, PATCH, DELETE tesztek országokkal

it('works with POST routes and countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::post('submit', function () {
        return 'submitted';
    })->name('submit')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en-us.submit'))->toBeTrue();
    expect($routes->hasNamedRoute('hu-hu.submit'))->toBeTrue();

    $enRoute = $routes->getByName('en-us.submit');
    $huRoute = $routes->getByName('hu-hu.submit');

    expect($enRoute->methods())->toBe(['POST']);
    expect($huRoute->methods())->toBe(['POST']);
});

it('works with PUT routes and countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::put('update/{id}', function ($id) {
        return "updated $id";
    })->name('update')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en-us.update'))->toBeTrue();
    expect($routes->hasNamedRoute('hu-hu.update'))->toBeTrue();

    $enRoute = $routes->getByName('en-us.update');
    expect($enRoute->methods())->toBe(['PUT']);
});

it('works with PATCH routes and countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::patch('patch/{id}', function ($id) {
        return "patched $id";
    })->name('patch')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en-us.patch'))->toBeTrue();
    expect($routes->hasNamedRoute('hu-hu.patch'))->toBeTrue();
});

it('works with DELETE routes and countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::delete('delete/{id}', function ($id) {
        return "deleted $id";
    })->name('delete')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en-us.delete'))->toBeTrue();
    expect($routes->hasNamedRoute('hu-hu.delete'))->toBeTrue();
});

// localizedExcept tesztek országokkal

it('localizedExcept works with countries', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
        'de' => 'de',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localizedExcept('de');

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en-us.example'))->toBeTrue();
    expect($routes->hasNamedRoute('hu-hu.example'))->toBeTrue();
    expect($routes->hasNamedRoute('de-de.example'))->toBeFalse();
});

// Middleware tesztek

it('adds SetCountryFromRoute middleware when use_countries is enabled', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $middleware = $enRoute->middleware();

    expect($middleware)->toContain(\LarasoftHU\LocalizedRoutesPlus\Middleware\SetCountryFromRoute::class);
});

it('country and locale getters work correctly', function () {
    config()->set('localized-routes-plus.use_countries', true);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.countries', [
        'en' => 'us',
        'hu' => 'hu',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    $enRoute = $routes->getByName('en-us.example');
    $huRoute = $routes->getByName('hu-hu.example');

    expect($enRoute->getLocale())->toBe('en');
    expect($enRoute->getCountry())->toBe('us');

    expect($huRoute->getLocale())->toBe('hu');
    expect($huRoute->getCountry())->toBe('hu');
});
