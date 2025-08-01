<?php

use Illuminate\Support\Facades\Route;

it('can chain localized method after Route::get', function () {
    $route = Route::get('test', function () {
        return 'test';
    })->localized();

    expect($route)->toBeInstanceOf(Illuminate\Routing\Route::class);
});

it('set localized route name', function () {
    $route = Route::get('test', function () {
        return 'test';
    })->name('test')->localized();

    expect($route->getName())->toBe('en.test');
});

it('creates routes for all locales', function () {

    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);
    config()->set('localized-routes-plus.default_locale', 'en');

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en.example route
    expect($routes->hasNamedRoute('en.example'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu.example route is
    expect($routes->hasNamedRoute('hu.example'))->toBeTrue();

    // Ellenőrizzük, hogy mindkét route ugyanarra az URI-ra mutat
    $enRoute = $routes->getByName('en.example');
    $huRoute = $routes->getByName('hu.example');

    expect($enRoute->uri())->toBe('example');
    expect($huRoute->uri())->toBe('hu/example');
});

it('creates routes for all locales with prefix for default locale in url enabled', function () {
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', true);
    config()->set('localized-routes-plus.default_locale', 'en');

    Route::get('apple/example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.example'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.example'))->toBeTrue();

    $enRoute = $routes->getByName('en.example');
    $huRoute = $routes->getByName('hu.example');

    expect($enRoute->uri())->toBe('en/apple/example');
    expect($huRoute->uri())->toBe('hu/apple/example');
});

it('works with different call order', function () {
    Route::get('reverse', function () {
        return 'reverse';
    })->name('reverse')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en.reverse route
    expect($routes->hasNamedRoute('en.reverse'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu.reverse route is
    expect($routes->hasNamedRoute('hu.reverse'))->toBeTrue();
});

// POST route teszt
it('works with POST routes', function () {
    Route::post('submit', function () {
        return 'submitted';
    })->name('submit')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.submit'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.submit'))->toBeTrue();

    $enRoute = $routes->getByName('en.submit');
    $huRoute = $routes->getByName('hu.submit');

    expect($enRoute->methods())->toBe(['POST']);
    expect($huRoute->methods())->toBe(['POST']);
});

// PUT route teszt
it('works with PUT routes', function () {
    Route::put('update/{id}', function ($id) {
        return "updated $id";
    })->name('update')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.update'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.update'))->toBeTrue();

    $enRoute = $routes->getByName('en.update');
    expect($enRoute->methods())->toBe(['PUT']);
});

// PATCH route teszt
it('works with PATCH routes', function () {
    Route::patch('patch/{id}', function ($id) {
        return "patched $id";
    })->name('patch')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.patch'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.patch'))->toBeTrue();
});

// DELETE route teszt
it('works with DELETE routes', function () {
    Route::delete('delete/{id}', function ($id) {
        return "deleted $id";
    })->name('delete')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.delete'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.delete'))->toBeTrue();
});

// Route::match teszt
it('works with Route::match', function () {
    Route::match(['GET', 'POST'], 'multi', function () {
        return 'multi method';
    })->name('multi')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.multi'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.multi'))->toBeTrue();

    $enRoute = $routes->getByName('en.multi');
    expect($enRoute->methods())->toBe(['GET', 'POST', 'HEAD']);
});

// Route::any teszt
it('works with Route::any', function () {
    Route::any('any-method', function () {
        return 'any method';
    })->name('any')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    expect($routes->hasNamedRoute('en.any'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.any'))->toBeTrue();
});

test('resource routes create routes for all locales', function () {
    // Létrehozunk egy lokalizált resource route-ot
    Route::resource('posts', 'PostController')->localized()->names('posts');

    // Ellenőrizzük, hogy mindkét locale-hoz létrejöttek a route-ok
    $routes = Route::getRoutes();

    $routeNames = [];
    $routeUris = [];
    foreach ($routes as $route) {
        if ($route->getName()) {
            $routeNames[] = $route->getName();
            $routeUris[] = $route->uri();
        }
    }
    // Default locale (en) routes
    expect($routeNames)->toContain('en.posts.index');
    expect($routeNames)->toContain('en.posts.create');
    expect($routeNames)->toContain('en.posts.store');
    expect($routeNames)->toContain('en.posts.show');
    expect($routeNames)->toContain('en.posts.edit');
    expect($routeNames)->toContain('en.posts.update');
    expect($routeNames)->toContain('en.posts.destroy');

    // Hungarian locale routes
    expect($routeNames)->toContain('hu.posts.index');
    expect($routeNames)->toContain('hu.posts.create');
    expect($routeNames)->toContain('hu.posts.store');
    expect($routeNames)->toContain('hu.posts.show');
    expect($routeNames)->toContain('hu.posts.edit');
    expect($routeNames)->toContain('hu.posts.update');
    expect($routeNames)->toContain('hu.posts.destroy');

});

test('resource routes create routes for all locales with custom prefix', function () {
    // Létrehozunk egy lokalizált resource route-ot
    Route::resource('apple/posts', 'PostController')->localized()->names('posts');

    // Ellenőrizzük, hogy mindkét locale-hoz létrejöttek a route-ok
    $routes = Route::getRoutes();

    foreach ($routes as $route) {
        if ($route->getName() && str_contains($route->uri(), 'apple/posts')) {
            $name = $route->getName();
            // dd($name);
            // Get locale working
            $locale = explode('.', $name)[0];
            expect($route->getLocale())->toBe($locale);
            if (str_contains($name, config('localized-routes-plus.default_locale'))) {
                expect($route->uri())->not()->toContain(config('localized-routes-plus.default_locale'));
            } else {
                expect($route->uri())->toContain($locale.'/apple/posts');
            }
        }
    }
});

test('resource routes create routes for all locales with custom prefix and use_route_prefix_in_default_locale is true', function () {
    // Létrehozunk egy lokalizált resource route-ot
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', true);
    Route::resource('apple/posts', 'PostController')->localized()->names('posts');

    // Ellenőrizzük, hogy mindkét locale-hoz létrejöttek a route-ok
    $routes = Route::getRoutes();
    $locales = config('localized-routes-plus.locales');
    $foundLocales = [];

    foreach ($routes as $route) {
        if ($route->getName() && str_contains($route->uri(), 'apple/posts')) {
            $name = $route->getName();
            $locale = explode('.', $name)[0];
            $foundLocales[] = $locale;
            // Get locale working
            expect($route->getLocale())->toBe($locale);
            expect($route->uri())->toContain($locale.'/apple/posts');
        }
    }

    $foundLocales = array_unique($foundLocales);
    $missingLocales = array_diff($locales, $foundLocales);

    expect($missingLocales)->toBe([]);
});

test('resource routes with custom names create routes for all locales', function () {
    // Létrehozunk egy lokalizált resource route-ot egyedi nevekkel
    Route::resource('articles', 'ArticleController')
        ->names([
            'index' => 'articles.list',
            'create' => 'articles.new',
        ])
        ->localized();

    // Ellenőrizzük, hogy mindkét locale-hoz létrejöttek a route-ok
    $routes = Route::getRoutes();

    $routeNames = [];
    foreach ($routes as $route) {
        if ($route->getName()) {
            $routeNames[] = $route->getName();
        }
    }

    // Default locale (en) routes
    expect($routeNames)->toContain('en.articles.list');
    expect($routeNames)->toContain('en.articles.new');

    // Hungarian locale routes
    expect($routeNames)->toContain('hu.articles.list');
    expect($routeNames)->toContain('hu.articles.new');
});

test('localizedExcept creates routes for all locales except the given ones', function () {
    // Konfigurálunk több nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de', 'fr']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot ami kizárja a 'de' és 'fr' nyelveket
    Route::get('test-except', function () {
        return 'test except';
    })->name('test.except')->localizedExcept(['de', 'fr']);

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en.test.except route
    expect($routes->hasNamedRoute('en.test.except'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu.test.except route is
    expect($routes->hasNamedRoute('hu.test.except'))->toBeTrue();

    // Ellenőrizzük, hogy NEM létezik a de.test.except route
    expect($routes->hasNamedRoute('de.test.except'))->toBeFalse();

    // Ellenőrizzük, hogy NEM létezik a fr.test.except route
    expect($routes->hasNamedRoute('fr.test.except'))->toBeFalse();
});

test('localizedExcept with empty array creates routes for all locales', function () {
    // Konfigurálunk nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot ami nem zár ki semmit (üres tömb)
    Route::get('test-no-except', function () {
        return 'test no except';
    })->name('test.no.except')->localizedExcept([]);

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik minden nyelvre
    expect($routes->hasNamedRoute('en.test.no.except'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.test.no.except'))->toBeTrue();
    expect($routes->hasNamedRoute('de.test.no.except'))->toBeTrue();
});

test('localized with specific locales creates routes only for given locales', function () {
    // Konfigurálunk több nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de', 'fr']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot csak a 'en' és 'hu' nyelvekhez
    Route::get('test-specific', function () {
        return 'test specific';
    })->name('test.specific')->localized(['en', 'hu']);

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en.test.specific route
    expect($routes->hasNamedRoute('en.test.specific'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu.test.specific route is
    expect($routes->hasNamedRoute('hu.test.specific'))->toBeTrue();

    // Ellenőrizzük, hogy NEM létezik a de.test.specific route
    expect($routes->hasNamedRoute('de.test.specific'))->toBeFalse();

    // Ellenőrizzük, hogy NEM létezik a fr.test.specific route
    expect($routes->hasNamedRoute('fr.test.specific'))->toBeFalse();
});

test('localized with single locale string creates route only for that locale', function () {
    // Konfigurálunk több nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot csak a 'hu' nyelvhez (string formában)
    Route::get('test-single', function () {
        return 'test single';
    })->name('test.single')->localized('hu');

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik a hu.test.single route
    expect($routes->hasNamedRoute('hu.test.single'))->toBeTrue();

    // Ellenőrizzük, hogy NEM létezik az en.test.single route (mert nem volt a megadott nyelvek között)
    expect($routes->hasNamedRoute('en.test.single'))->toBeFalse();

    // Ellenőrizzük, hogy NEM létezik a de.test.single route
    expect($routes->hasNamedRoute('de.test.single'))->toBeFalse();
});

test('localized with empty array creates routes for all configured locales', function () {
    // Konfigurálunk nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot üres tömbbel (ez az alapértelmezett működés)
    Route::get('test-empty', function () {
        return 'test empty';
    })->name('test.empty')->localized([]);

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik minden konfigurált nyelvre
    expect($routes->hasNamedRoute('en.test.empty'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.test.empty'))->toBeTrue();
});

test('localizedExcept with string parameter excludes single locale', function () {
    // Konfigurálunk nyelveket a teszteléshez
    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
    config()->set('localized-routes-plus.default_locale', 'en');
    config()->set('localized-routes-plus.use_route_prefix_in_default_locale', false);

    // Létrehozunk egy route-ot ami kizárja a 'de' nyelvet (string formában)
    Route::get('test-except-string', function () {
        return 'test except string';
    })->name('test.except.string')->localizedExcept('de');

    $router = app('router');
    $routes = $router->getRoutes();

    // Ellenőrizzük, hogy létezik az en.test.except.string route
    expect($routes->hasNamedRoute('en.test.except.string'))->toBeTrue();

    // Ellenőrizzük, hogy létezik a hu.test.except.string route is
    expect($routes->hasNamedRoute('hu.test.except.string'))->toBeTrue();

    // Ellenőrizzük, hogy NEM létezik a de.test.except.string route
    expect($routes->hasNamedRoute('de.test.except.string'))->toBeFalse();
});
