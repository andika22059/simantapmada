<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Situs yang boleh mengakses API ini.
    // WAJIB memuat domain produksi frontend, kalau tidak browser akan
    // memblokir request (muncul "Network Error" / server tidak merespon).
    'allowed_origins' => [
        'https://mantapmada.com',
        'https://www.mantapmada.com',
        'http://mantapmada.com',
        'http://www.mantapmada.com',
        'http://localhost:5173',
        'http://localhost:4173',
        'http://127.0.0.1:5173',
    ],

    // Izinkan localhost port berapa pun saat pengembangan.
    'allowed_origins_patterns' => ['#^http://localhost(:\d+)?$#'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
