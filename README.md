# Laravel Localized Routes Plus

[![Latest Version on Packagist](https://img.shields.io/packagist/v/LarasoftHU/localized-routes-plus.svg?style=flat-square)](https://packagist.org/packages/LarasoftHU/localized-routes-plus)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/LarasoftHU/localized-routes-plus/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/LarasoftHU/localized-routes-plus/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/LarasoftHU/localized-routes-plus/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/LarasoftHU/localized-routes-plus/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/LarasoftHU/localized-routes-plus.svg?style=flat-square)](https://packagist.org/packages/LarasoftHU/localized-routes-plus)

A powerful Laravel package for creating localized routes with advanced features including subdomain support, country-specific routing, and automatic locale management.

## Features

🌍 **Multiple Localization Strategies**
- URL prefix-based localization (`/en/page`, `/hu/page`)
- Subdomain-based localization (`en.example.com`, `hu.example.com`)
- Country-specific routing (`/en-us/page`, `/en-ca/page`)

🚀 **Framework Integration**
- Seamless Laravel Route integration
- Automatic middleware registration
- Resource route support
- Custom route model binding

⚙️ **Flexible Configuration**
- Whitelist/blacklist locales
- Configurable separators
- Multiple domains per locale
- Default locale customization

🔧 **Developer-Friendly**
- Intuitive API design
- Helper methods for URL generation
- Comprehensive testing
- Full IDE support

## Installation

Install the package via Composer:

```bash
composer require larasofthu/localized-routes-plus
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="localized-routes-plus-config"
```

## Quick Start

### Basic Usage

Create localized routes by chaining the `localized()` method:

```php
use Illuminate\Support\Facades\Route;

// Creates routes for all configured locales
Route::get('about', function () {
    return view('about');
})->name('about')->localized();

// Results in:
// /about (default locale)
// /hu/about (Hungarian)
// /de/about (German)
```

### Resource Routes

```php
Route::resource('posts', PostController::class)
    ->names('posts')
    ->localized();

// Creates localized versions of all resource routes:
// en.posts.index, en.posts.create, en.posts.store, etc.
// hu.posts.index, hu.posts.create, hu.posts.store, etc.
```

### Helper Functions

The package provides convenient helper functions for common tasks:

```php
// Get current route in different locale
$germanUrl = current_route('de');

// Check route name (works for any locale)
if (route_is('about')) {
    // Current page is about page
}

// Generate localized routes
$url = localized_route('products.show', ['product' => $product]);
```

```blade
{{-- Language switcher in Blade --}}
<div class="language-switcher">
    @foreach(config('localized-routes-plus.locales') as $locale)
        <a href="{{ current_route($locale) }}">{{ $locale }}</a>
    @endforeach
</div>
```

## Configuration

The configuration file (`config/localized-routes-plus.php`) contains all available options:

```php
return [
    // Available locales
    'locales' => ['en', 'hu', 'de'],
    
    // Default locale
    'default_locale' => 'en',
    
    // Include prefix for default locale in URLs
    'use_route_prefix_in_default_locale' => false,
    
    // Subdomain configuration
    'use_subdomains_instead_of_prefixes' => false,
    'domains' => [
        'en' => 'example.com',
        'hu' => 'example.hu',
        'de' => 'de.example.com',
    ],
    
    // Country-specific routing
    'use_countries' => false,
    'country_path_separator' => 'dash', // 'dash' or 'slash'
    'countries' => [
        'en' => 'us',
        'hu' => 'hu',
        'de' => 'de',
    ],
];
```

## URL Prefix Localization

### Basic Configuration

```php
// config/localized-routes-plus.php
'locales' => ['en', 'hu', 'de'],
'default_locale' => 'en',
'use_route_prefix_in_default_locale' => false,
```

### Route Examples

```php
Route::get('products', [ProductController::class, 'index'])
    ->name('products.index')
    ->localized();

// Generated routes:
// GET /products -> en.products.index (default locale, no prefix)
// GET /hu/products -> hu.products.index
// GET /de/products -> de.products.index
```

### Including Default Locale in URLs

```php
// config/localized-routes-plus.php
'use_route_prefix_in_default_locale' => true,

// Results in:
// GET /en/products -> en.products.index
// GET /hu/products -> hu.products.index
// GET /de/products -> de.products.index
```

## Subdomain Localization

### Configuration

```php
// config/localized-routes-plus.php
'use_subdomains_instead_of_prefixes' => true,
'domains' => [
    'en' => 'example.com',
    'hu' => 'example.hu',
    'de' => 'de.example.com',
],
```

### Multiple Domains per Locale

```php
'domains' => [
    'en' => 'example.com',
    'hu' => 'example.hu',
    'de' => [
        'de.example.com',
        'de2.example.com',
        'example.de'
    ],
],
```

### Route Examples

```php
Route::get('products', [ProductController::class, 'index'])
    ->name('products.index')
    ->localized();

// Generated routes:
// example.com/products -> en.products.index
// example.hu/products -> hu.products.index
// de.example.com/products -> de.products.index
// de2.example.com/products -> de.products.index (additional domain)
// example.de/products -> de.products.index (additional domain)
```

## Country-Specific Routing

### Configuration

```php
// config/localized-routes-plus.php
'use_countries' => true,
'country_path_separator' => 'dash',
'countries' => [
    'en' => 'us',
    'hu' => 'hu',
    'de' => 'de',
],
```

### Multiple Countries per Locale

```php
'countries' => [
    'en' => ['us', 'ca', 'gb'],
    'hu' => 'hu',
    'de' => 'de',
],
```

### Path Separators

```php
// Dash separator (default)
'country_path_separator' => 'dash',
// Results in: /en-us/products, /en-ca/products

// Slash separator
'country_path_separator' => 'slash',
// Results in: /en/us/products, /en/ca/products
```

### Route Examples

```php
Route::get('products', [ProductController::class, 'index'])
    ->name('products.index')
    ->localized();

// With dash separator:
// GET /en-us/products -> en-us.products.index
// GET /en-ca/products -> en-ca.products.index
```

## Selective Localization

### Whitelist Specific Locales

```php
// Only create routes for English and Hungarian
Route::get('admin', [AdminController::class, 'index'])
    ->name('admin.index')
    ->localized(['en', 'hu']);

// Single locale
Route::get('terms', function () {
    return view('terms');
})->name('terms')->localized('en');
```

### Blacklist Specific Locales

```php
// Create routes for all locales except German
Route::get('news', [NewsController::class, 'index'])
    ->name('news.index')
    ->localizedExcept('de');

// Multiple locales
Route::get('blog', [BlogController::class, 'index'])
    ->name('blog.index')
    ->localizedExcept(['de', 'fr']);
```

## Advanced Usage

### Route Model Binding

```php
Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show')
    ->localized();

// Works with model binding:
// /posts/my-post-slug -> en.posts.show
// /hu/posts/my-post-slug -> hu.posts.show
```

### Route Parameters

```php
Route::get('categories/{category}/products/{product}', [ProductController::class, 'show'])
    ->name('products.show')
    ->localized();

// Generated routes handle parameters correctly:
// /categories/electronics/products/laptop -> en.products.show
// /hu/categories/electronics/products/laptop -> hu.products.show
```

### Middleware Integration

The package automatically registers middleware to set the application locale:

```php
// Automatically applied to localized routes
SetLocaleFromRoute::class  // Sets App::setLocale()
SetCountryFromRoute::class // Sets App::setCountry() (when countries enabled)
```

## Helper Methods

### Route Switching

```php
// In your views or controllers
$currentRoute = request()->route();

// Get route for different locale
$germanRoute = $currentRoute->locale('de');
$germanUrl = $germanRoute->getUrl();

// With countries
$usRoute = $currentRoute->locale('en', 'us');
$canadaRoute = $currentRoute->locale('en', 'ca');
```

### URL Generation

```php
// Standard Laravel route() helper works
$url = route('products.index'); // Current locale
$hungarianUrl = route('hu.products.index'); // Specific locale

// With countries
$usUrl = route('en-us.products.index');
$canadaUrl = route('en-ca.products.index');

// Using helper methods
$route = request()->route();
$germanUrl = $route->getUrl('de');
$usUrl = $route->getUrl('en', 'us'); // With country
```

### Route Information

```php
$route = request()->route();

// Get locale and country
$locale = $route->getLocale();    // 'en'
$country = $route->getCountry();  // 'us' (if countries enabled)

// Get route name without locale prefix
$safeName = $route->getSafeName(); // 'products.index'

// Check if route matches a name
if ($route->is('products.index')) {
    // Current route is products.index (any locale)
}
```

### Global Helper Functions

The package provides convenient global helper functions:

#### `current_route()`

Get the current route URL in a different locale:

```php
// Get current route in German
$germanUrl = current_route('de');

// With countries - EN-US
$usUrl = current_route('en', 'us');

// With custom parameters
$url = current_route('hu', null, ['id' => 123]);

// Relative URL
$relativeUrl = current_route('de', null, [], false);
```

#### `route_is()`

Check if the current route matches a name (locale-agnostic):

```php
// Traditional Laravel way (locale-specific)
if (request()->route()->getName() === 'en.products.index') {
    // Only matches English version
}

// Using route_is() helper (works for any locale)
if (route_is('products.index')) {
    // Matches en.products.index, hu.products.index, de.products.index, etc.
    // you can use it in balde templates see below
}

<nav class="...@if(route_is('products.index')selected@endif">...</nav>

```

#### `localized_route()`

Generate localized route URLs:

```php
// Generate route for current locale
$url = localized_route('products.show', ['product' => $product]);

// Equivalent to:
$locale = app()->getLocale();
$url = route($locale . '.products.show', ['product' => $product]);

// Automatic with countries (when enabled)
$url = localized_route('products.show', ['product' => $product]);
// Uses current locale and country automatically


// You can add custom locale and country if you want
$url = localized_route('products.show', ['product' => $product], $absolute, $locale, $country);
```

## Localized URI Translation

The package supports translating URI segments using Laravel's language files. This allows you to have different URL structures for different locales while keeping the same route logic.

### Setup

Create language files for your routes in the `lang/{locale}/routes.php` files:

```php
// lang/en/routes.php
<?php

return [
    'news.index' => '/news',
    'products.index' => '/products',
    'about' => '/about',
    'contact' => '/contact',
];
```

```php
// lang/hu/routes.php
<?php

return [
    'news.index' => '/hirek',
    'products.index' => '/termekek', 
    'about' => '/rolunk',
    'contact' => '/kapcsolat',
];
```

```php
// lang/de/routes.php
<?php

return [
    'news.index' => '/nachrichten',
    'products.index' => '/produkte',
    'about' => '/uber-uns',
    'contact' => '/kontakt',
];
```

### Usage

Define your routes normally and the package will automatically use the translated URIs:

```php
// routes/web.php
Route::get('/news', [NewsController::class, 'index'])
    ->name('news.index')
    ->localized();

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index')
    ->localized();

Route::get('/about', function () {
    return view('about');
})->name('about')->localized();
```

### Generated Routes

The above configuration will generate the following routes:

```
// English (default locale)
GET /news -> en.news.index
GET /products -> en.products.index  
GET /about -> en.about

// Hungarian  
GET /hu/hirek -> hu.news.index
GET /hu/termekek -> hu.products.index
GET /hu/rolunk -> hu.about

// German
GET /de/nachrichten -> de.news.index
GET /de/produkte -> de.products.index
GET /de/uber-uns -> de.about
```

### With Route Parameters

You can also translate URIs that contain parameters:

```php
// routes/web.php
Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show')
    ->localized();

Route::get('/categories/{category}/products', [ProductController::class, 'index'])
    ->name('categories.products')
    ->localized();
```

```php
// lang/hu/routes.php
return [
    'products.show' => '/termekek/{product}',
    'categories.products' => '/kategoriak/{category}/termekek',
];

// lang/de/routes.php  
return [
    'products.show' => '/produkte/{product}',
    'categories.products' => '/kategorien/{category}/produkte',
];
```

### Important Notes

- The translation key must match the route name exactly (use `getSafeName()` without locale prefix)
- If no translation is found, the original URI is used
- Parameters must be preserved in translated URIs (`{parameter}`)
- This works with all localization strategies (prefixes, subdomains, countries)

## Blade Helpers

### Language Switcher

```blade
{{-- Basic language switcher using helper function --}}
<div class="language-switcher">
    @foreach(config('localized-routes-plus.locales') as $locale)
        @if($locale !== app()->getLocale())
            <a href="{{ current_route($locale) }}">
                {{ strtoupper($locale) }}
            </a>
        @endif
    @endforeach
</div>

{{-- Alternative using route methods --}}
<div class="language-switcher">
    @foreach(config('localized-routes-plus.locales') as $locale)
        @if($locale !== app()->getLocale())
            <a href="{{ request()->route()->getUrl($locale) }}">
                {{ strtoupper($locale) }}
            </a>
        @endif
    @endforeach
</div>

{{-- With countries --}}
<div class="country-switcher">
    <a href="{{ current_route('en', 'us') }}">🇺🇸 US</a>
    <a href="{{ current_route('en', 'ca') }}">🇨🇦 Canada</a>
    <a href="{{ current_route('en', 'gb') }}">🇬🇧 UK</a>
</div>
```

### Current Locale Detection

```blade
{{-- Check current locale --}}
@if(app()->getLocale() === 'hu')
    <p>Hungarian content</p>
@endif

{{-- Check if route is localized --}}
@if(request()->route() instanceof \LarasoftHU\LocalizedRoutesPlus\LocalizedRoute)
    <p>This is a localized route</p>
@endif

{{-- Using route_is() helper --}}
@if(route_is('products.index'))
    <p>This is the products index page (any locale)</p>
@endif

{{-- Generate links using localized_route() helper --}}
<a href="{{ localized_route('products.show', ['product' => $product]) }}">
    View Product
</a>

{{-- Navigation menu with active state detection --}}
<nav>
    <a href="{{ localized_route('home') }}" 
       class="{{ route_is('home') ? 'active' : '' }}">
        Home
    </a>
    <a href="{{ localized_route('products.index') }}" 
       class="{{ route_is('products.*') ? 'active' : '' }}">
        Products  
    </a>
    <a href="{{ localized_route('about') }}" 
       class="{{ route_is('about') ? 'active' : '' }}">
        About
    </a>
</nav>
```

## Testing

The package includes comprehensive tests covering all functionality:

```bash
# Run all tests
composer test

# Run specific test suites
./vendor/bin/pest tests/Feature/LocalizedRoutesTest.php
./vendor/bin/pest tests/Feature/CountryRoutesTest.php
./vendor/bin/pest tests/Feature/DomainRoutesTest.php

# Run with coverage
composer test-coverage
```

## API Reference

### LocalizedRoute Methods

| Method | Description | Example |
|--------|-------------|---------|
| `localized($locales = [])` | Create localized versions | `->localized(['en', 'hu'])` |
| `localizedExcept($locales = [])` | Exclude specific locales | `->localizedExcept('de')` |
| `locale($locale, $country = null)` | Get route for locale | `$route->locale('hu')` |
| `getUrl($locale = null, $country = null)` | Generate URL for locale | `$route->getUrl('de')` |
| `getLocale()` | Get route locale | `$route->getLocale()` |
| `getCountry()` | Get route country | `$route->getCountry()` |
| `getSafeName()` | Get name without locale prefix | `$route->getSafeName()` |
| `is($name)` | Check route name (locale-agnostic) | `$route->is('products.index')` |

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `locales` | `array` | `['en']` | Available locales |
| `default_locale` | `string` | `'en'` | Default application locale |
| `use_route_prefix_in_default_locale` | `bool` | `false` | Include prefix for default locale |
| `use_subdomains_instead_of_prefixes` | `bool` | `false` | Use subdomains instead of prefixes |
| `domains` | `array` | `[]` | Domain mapping for locales |
| `use_countries` | `bool` | `false` | Enable country-specific routing |
| `country_path_separator` | `string` | `'dash'` | Separator between locale and country |
| `countries` | `array` | `[]` | Country mapping for locales |

### Global Helper Functions

| Function | Description | Example |
|----------|-------------|---------|
| `current_route($locale, $country, $parameters, $absolute)` | Get current route URL in different locale | `current_route('de')` |
| `route_is($name)` | Check route name (locale-agnostic) | `route_is('products.index')` |
| `localized_route($name, $parameters, $absolute, $locale, $country)` | Generate localized route URL | `localized_route('products.show', $product)` |

### Middleware

| Class | Description | Auto-Applied |
|-------|-------------|--------------|
| `SetLocaleFromRoute` | Sets `App::setLocale()` from route | ✅ Yes |
| `SetCountryFromRoute` | Sets `App::setCountry()` from route | ✅ When countries enabled |

## Examples

### E-commerce Site

```php
// Product routes with localization
Route::prefix('shop')->group(function () {
    Route::get('/', [ShopController::class, 'index'])
        ->name('shop.index')
        ->localized();
        
    Route::get('categories/{category}', [CategoryController::class, 'show'])
        ->name('shop.categories.show')
        ->localized();
        
    Route::resource('products', ProductController::class)
        ->only(['index', 'show'])
        ->names('shop.products')
        ->localized();
});

// Admin routes (English only)
Route::prefix('admin')->group(function () {
    Route::resource('products', AdminProductController::class)
        ->names('admin.products')
        ->localized('en');
});
```

### Multi-Country Site

```php
// config/localized-routes-plus.php
'use_countries' => true,
'countries' => [
    'en' => ['us', 'ca', 'gb', 'au'],
    'es' => ['es', 'mx', 'ar'],
    'fr' => ['fr', 'ca'],
],

// Routes
Route::get('pricing', [PricingController::class, 'index'])
    ->name('pricing')
    ->localized();

// Results in routes like:
// /en-us/pricing, /en-ca/pricing, /en-gb/pricing, /en-au/pricing
// /es-es/pricing, /es-mx/pricing, /es-ar/pricing
// /fr-fr/pricing, /fr-ca/pricing
```

### Subdomain Setup

```php
// config/localized-routes-plus.php
'use_subdomains_instead_of_prefixes' => true,
'domains' => [
    'en' => 'example.com',
    'de' => ['de.example.com', 'example.de'],
    'fr' => 'fr.example.com',
],

// All routes automatically work on their respective domains
Route::get('/', [HomeController::class, 'index'])
    ->name('home')
    ->localized();

// example.com/ -> en.home
// de.example.com/ -> de.home
// example.de/ -> de.home (additional domain)
// fr.example.com/ -> fr.home
```

## Troubleshooting

### Common Issues

**Route names must be set before `localized()`**
```php
// ❌ Wrong
Route::get('products', ProductController::class)->localized()->name('products');

// ✅ Correct
Route::get('products', ProductController::class)->name('products')->localized();
```

**Missing domain configuration for subdomains**
```php
// Make sure all locales have corresponding domains when using subdomains
'domains' => [
    'en' => 'example.com',
    'hu' => 'example.hu', // Don't forget this!
],
```

**Country parameter required when countries enabled**
```php
// ❌ Wrong (when use_countries is true)
$route->locale('en');

// ✅ Correct
$route->locale('en', 'us');
```

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security vulnerabilities, please email fulopkapasi@gmail.com instead of using the issue tracker.

## Credits

- [Kapási Fülöp](https://github.com/LarasoftHU)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
