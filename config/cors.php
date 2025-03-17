<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie','admin/*'], // Autoriser les requêtes vers ces chemins spécifiés

    'allowed_methods' => ['*'], // Autorise toutes les méthodes HTTP (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => [
        'http://localhost:3000',  // Add your frontend URL here
        'http://127.0.0.1:3000', // Add for localhost if needed
    ],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Autorise tous les headers

    'exposed_headers' => [], // Optionnel : expose certains headers si nécessaire

    'max_age' => 0, // Définit le cache de pré-vol CORS en secondes. 0 signifie qu'il n'y a pas de mise en cache

    'supports_credentials' => true, // Indique si les cookies et les informations d'authentification sont envoyés avec la requête

];
