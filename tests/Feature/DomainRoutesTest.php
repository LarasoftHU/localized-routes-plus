<?php

use Illuminate\Support\Facades\Route;

it('Works with subdomain GET routes', function () {
    config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
    config()->set('localized-routes-plus.default_locale', 'en');

    config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
    config()->set('localized-routes-plus.domains', [
        'en' => 'example.com',
        'hu' => 'example.hu',
        'de' => 'de.example.com',
    ]);

    Route::get('example', function () {
        return 'example';
    })->name('example')->localized();

    $router = app('router');
    $routes = $router->getRoutes();

    foreach ($routes as $route) {
      if($route->getName() && str_contains($route->getName(), 'example')) {
        expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
        foreach (config('localized-routes-plus.locales') as $locale) {
          expect($route->uri())->not->toContain($locale);
        }
      }
    }
});

it('Works with subdomain POST routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::post('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain PUT routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::put('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain PATCH routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::patch('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain DELETE routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::delete('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain OPTIONS routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::options('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain ANY routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::any('example', function () {
      return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain Resource routes', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::resource('example', 'PostController')->names('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain Resource routes with custom prefix', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'apple.example.hu',
      'de' => 'de.example.com',
  ]);

  $allDomains = [
    'example.com',
    'apple.example.hu',
    'de.example.com',
  ];


  Route::resource('apple/example', 'PostController')->names('example')->localized();

  $routes = Route::getRoutes();

  // check that all of the route names exists
  foreach (config('localized-routes-plus.locales') as $locale) {
    expect($routes->getByName($locale.'.example.index'))->toBeTruthy();
    expect($routes->getByName($locale.'.example.create'))->toBeTruthy();
    expect($routes->getByName($locale.'.example.store'))->toBeTruthy();
    expect($routes->getByName($locale.'.example.show'))->toBeTruthy();
    expect($routes->getByName($locale.'.example.edit'))->toBeTruthy();
    expect($routes->getByName($locale.'.example.update'))->toBeTruthy();

    expect($routes->getByName($locale.'.example.index')->getLocale())->toBe($locale);
    expect($routes->getByName($locale.'.example.create')->getLocale())->toBe($locale);
    expect($routes->getByName($locale.'.example.store')->getLocale())->toBe($locale);
    expect($routes->getByName($locale.'.example.show')->getLocale())->toBe($locale);
    expect($routes->getByName($locale.'.example.edit')->getLocale())->toBe($locale);
    expect($routes->getByName($locale.'.example.update')->getLocale())->toBe($locale);
  }

  $foundDomains = [];
  $formattedRoutes = [];
  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      $foundDomains[] = $route->getDomain();
    }
    $formattedRoutes[] = [
      'name' => $route->getName(),
      'locale' => $route->getLocale(),
      'domain' => $route->getDomain(),
      'uri' => $route->uri(),
    ];
  }

  // Debug: remove duplicates to properly check missing domains
  $foundDomains = array_unique($foundDomains);
  $missingDomains = array_diff($allDomains, $foundDomains);
  // Debug information - uncomment to see what's happening
  // echo "All domains: " . print_r($allDomains, true);
  // echo "Found domains: " . print_r($foundDomains, true);
  // echo "Missing domains: " . print_r($missingDomains, true);
  
  expect($missingDomains)->toBe([]);
});


// localizedExcept

it('Works with subdomain Resource routes with localizedExcept', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::resource('example', 'PostController')->names('example')->localizedExcept('de');

  $router = app('router');
  $routes = $router->getRoutes();

  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      expect($route->getLocale())->not->toBe('de');
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
    }
  }
});

it('Works with subdomain Resource routes with custom prefix and localizedExcept', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ]);

  Route::resource('apple/example', 'PostController')->names('example')->localizedExcept('de');

  $router = app('router');
  $routes = $router->getRoutes();

  $formattedRoutes = [];
  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      expect($route->getLocale())->not->toBe('de');
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
      $formattedRoutes[] = [
        'name' => $route->getName(),
        'domain' => $route->getDomain(),
        'uri' => $route->uri(),
        'locale' => $route->getLocale(),
      ];
    }
  }
});

it('Works with multiple subdomain for single language', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);

  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => [
        'de.example.com',
        'de2.example.com',
        'example.de'
      ]
  ]);
  // for testing purposes
  $allDomains = [
    'example.com',
    'example.hu',
    'de.example.com',
    'de2.example.com',
    'example.de',
  ];

  Route::get('example', function () {
    return 'example';
  })->name('example')->localized();

  $router = app('router');
  $routes = $router->getRoutes();

  $foundDomains = [];
  $formattedRoutes = [];
  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      if(is_array(config('localized-routes-plus.domains')[$route->getLocale()])) {
        expect($route->getDomain())->toBeIn(config('localized-routes-plus.domains')[$route->getLocale()]);
      } else {
        expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      }
      $foundDomains[] = $route->getDomain();
      $formattedRoutes[] = [
        'name' => $route->getName(),
        'domain' => $route->getDomain(),
        'uri' => $route->uri(),
        'locale' => $route->getLocale(),
      ];
    }
  }

  $missingDomains = array_diff($allDomains, $foundDomains);

  expect($missingDomains)->toBe([]);

});

// resource

it('Works with multiple subdomain for single language Resource routes with custom prefix and localizedExcept', function () {
  config()->set('localized-routes-plus.use_subdomains_instead_of_prefixes', true);
  config()->set('localized-routes-plus.default_locale', 'en');

  config()->set('localized-routes-plus.locales', ['en', 'hu', 'de']);
  config()->set('localized-routes-plus.domains', [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => [
        'de.example.com',
        'de2.example.com',
        'example.de'
      ]
  ]);
  // for testing purposes - only include domains for locales that will be generated (excluding 'de')
  $allDomains = [
    'example.com',  // en
    'example.hu',   // hu
    // 'de' locale is excluded by localizedExcept('de')
  ];

  Route::resource('apple/example', 'PostController')->names('example')->localizedExcept('de');

  $router = app('router');
  $routes = $router->getRoutes();

  $foundDomains = [];
  $formattedRoutes = [];
  foreach ($routes as $route) {
    if($route->getName() && str_contains($route->getName(), 'example')) {
      if(is_array(config('localized-routes-plus.domains')[$route->getLocale()])) {
        expect($route->getDomain())->toBeIn(config('localized-routes-plus.domains')[$route->getLocale()]);
      } else {
        expect($route->getDomain())->toBe(config('localized-routes-plus.domains')[$route->getLocale()]);
      }
      $foundDomains[] = $route->getDomain();
      foreach (config('localized-routes-plus.locales') as $locale) {
        expect($route->uri())->not->toContain($locale);
      }
      $formattedRoutes[] = [
        'name' => $route->getName(),
        'domain' => $route->getDomain(),
        'uri' => $route->uri(),
        'locale' => $route->getLocale(),
      ];
    }
  }

  // Remove duplicates to properly check missing domains
  $foundDomains = array_unique($foundDomains);
  $missingDomains = array_diff($allDomains, $foundDomains);

  // Debug information
  // echo "Test 2 - All domains: " . print_r($allDomains, true);
  // echo "Test 2 - Found domains: " . print_r($foundDomains, true);
  // echo "Test 2 - Missing domains: " . print_r($missingDomains, true);

  expect($missingDomains)->toBe([]);
});