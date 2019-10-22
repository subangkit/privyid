<?php
return [
    'is_production' => env('PRIVYID_PRODUCTION'),
    'production' => [
        'merchant_key' => env('PRIVYID_MERCHANT_KEY'),
        'password' => env('PRIVYID_PASSWORD'),
        'user' => env('PRIVYID_USER'),
    ],
    'client_id' => env('PRIVYID_CLIENT_ID'),
    'secret_key' => env('PRIVYID_SECRET_KEY')
];
