<?php

// config for LarasoftHU/LocalizedRoutesPlus
return [

    'locales' => [
        env('APP_LOCALE', 'en'),
    ],

    'default_locale' => env('APP_LOCALE', 'en'),

    'use_subdomains_instead_of_prefixes' => env('LOCALIZED_ROUTES_USE_SUBDOMAINS', false),

    'domains' => [
        'en' => 'example.com',
        'hu' => 'example.hu',
        'de' => 'de.example.com',
    ],

    'use_route_prefix_in_default_locale' => false,

    // THIS WONT WORK WITH SUBDOMAINS
    'use_countries' => false,
    'country_path_separator' => 'dash', // 'dash' for /locale-country or 'slash' for /locale/country
    'countries' => [ // you can add lovercase country codes here, but it will be converted to uppercase
        'hu' => 'hu',
        'en' => 'us',
        'de' => 'gb',
    ],

    // the default country for the default locale
    'default_country' => 'us',
];
