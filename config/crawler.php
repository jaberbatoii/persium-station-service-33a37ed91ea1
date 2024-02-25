<?php

declare(strict_types = 1);

return [
    'DEFRA' => [
        'base_url' => env('DEFRA_BASE_URL', ''),
        'sources' => ["AURN", "AQE", "KCL", "NI", "SAQN", "WAQN", 'DEFRA'],
        'auth_user'  => '',
        'auth_password'  => '',
        'auth_secret'    => '',
        // latest data is unstable, so we don't save it to db
        'save_latest_data_to_db' => false,
    ],
    'ACOEM' => [
        'base_url'  => env('ACOEM_BASE_URL', 'https://api.airmonitors.net'),
        'sources' => ["ACOEM"],
        'auth_user'  => env('ACOEM_AUTH_USERNAME'),
        'auth_password'  => env('ACOEM_AUTH_PASSWORD'),
        'auth_secret'    => '',
        'save_latest_data_to_db' => true,
    ],
    'PurpleAir' => [
        'base_url'  => env('PURPLE_AQI_BASE_URL', 'https://api.purpleair.com'),
        'sources' => ["PurpleAir"],
        'auth_user'  => '',
        'auth_password'  => '',
        'auth_secret'   => env('PURPLE_AQI_API_KEY'),
        'save_latest_data_to_db' => true,
    ],
    'CEM'   => [
        'base_url'  => env('CEM_BASE_URL', 'https://api.purpleair.com'),
        'sources' => ["CEM"],
        'auth_user'  => '',
        'auth_password'  => '',
        'auth_secret'    => '',
        'save_latest_data_to_db' => true,
    ]
];
