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
    })->localized()->name('test');

    expect($route->getName())->toBe('en.test');
});

it('creates routes for all locales', function () {
    Route::get('example', function () {
        return 'example';
    })->localized()->name('example');

    $router = app('router');
    $routes = $router->getRoutes();
    
    // Ellenőrizzük, hogy létezik az en.example route
    expect($routes->hasNamedRoute('en.example'))->toBeTrue();
    
    // Ellenőrizzük, hogy létezik a hu.example route is
    expect($routes->hasNamedRoute('hu.example'))->toBeTrue();
    
    // Ellenőrizzük, hogy mindkét route ugyanarra az URI-ra mutat
    $enRoute = $routes->getByName('en.example');
    $huRoute = $routes->getByName('hu.example');
    
    expect($enRoute->uri())->toBe($huRoute->uri());
    expect($enRoute->uri())->toBe('example');
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
    })->localized()->name('submit');

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
    })->localized()->name('update');

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
    })->localized()->name('patch');

    $router = app('router');
    $routes = $router->getRoutes();
    
    expect($routes->hasNamedRoute('en.patch'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.patch'))->toBeTrue();
});

// DELETE route teszt
it('works with DELETE routes', function () {
    Route::delete('delete/{id}', function ($id) {
        return "deleted $id";
    })->localized()->name('delete');

    $router = app('router');
    $routes = $router->getRoutes();
    
    expect($routes->hasNamedRoute('en.delete'))->toBeTrue();
    expect($routes->hasNamedRoute('hu.delete'))->toBeTrue();
});

// Route::match teszt
it('works with Route::match', function () {
    Route::match(['GET', 'POST'], 'multi', function () {
        return 'multi method';
    })->localized()->name('multi');

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
    })->localized()->name('any');

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
    foreach ($routes as $route) {
        if ($route->getName()) {
            $routeNames[] = $route->getName();
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