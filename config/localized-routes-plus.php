<?php

// config for LarasoftHU/LocalizedRoutesPlus
return [

    'locales' => [
        env('APP_LOCALE', 'en'),
    ],

    'default_locale' => env('APP_LOCALE', 'en'),

    'use_subdomains_instead_of_prefixes' => env('LOCALIZED_ROUTES_USE_SUBDOMAINS', false),

    'use_route_prefix_in_default_locale' => false,
];
