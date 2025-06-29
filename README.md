# This is my package localized-routes-plus

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kapasifulop/localized-routes-plus.svg?style=flat-square)](https://packagist.org/packages/kapasifulop/localized-routes-plus)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kapasifulop/localized-routes-plus/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kapasifulop/localized-routes-plus/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kapasifulop/localized-routes-plus/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kapasifulop/localized-routes-plus/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kapasifulop/localized-routes-plus.svg?style=flat-square)](https://packagist.org/packages/kapasifulop/localized-routes-plus)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/localized-routes-plus.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/localized-routes-plus)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require kapasifulop/localized-routes-plus
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="localized-routes-plus-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="localized-routes-plus-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="localized-routes-plus-views"
```

## Usage

Add localized() to localize a route, you cannot use ->name after localized() for not resource routes!

```php
// whitelist
->localized('de')
->localized(['en', 'de'])

// blacklist
->localizedExcept('de')
->localizedExcept(['en', 'de'])
```

### In the config you can specify to use subdomains

```php

  // 1. 
  'use_subdomains_instead_of_prefixes' => true,

  // 2.
  'localized-routes-plus.domains' => [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => 'de.example.com',
  ];

  // OR
    'localized-routes-plus.domains' => [
      'en' => 'example.com',
      'hu' => 'example.hu',
      'de' => [
        'de.example.com',
        'de2.example.com',
        'example.de'
      ]
  ];
```

```php

Route::get('example', function () {
  return 'hellp'
})->name('example')->localized();

Route::resource('apple/example', 'PostController')->names('example')->localizedExcept('de');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Kapási Fülöp](https://github.com/kapasifulop)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
