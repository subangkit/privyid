<?php
return [
    'is_production' => env('PRIVYID_PRODUCTION'),
    'production' => [
        'merchant_key' => env('PRIVYID_MERCHANT_KEY'),
        'password' => env('PRIVYID_PASSWORD'),
        'user' => env('PRIVYID_USER'),
    ]
];
