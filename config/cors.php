<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Seul le client React Native hébergé sur Vercel est autorisé à interroger
    | cette API. Toute autre origine est refusée par le middleware HandleCors.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://b2lp-ryan.vercel.app'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // false : l'authentification est stateless par Bearer token, pas par cookie de session
    'supports_credentials' => false,
];
