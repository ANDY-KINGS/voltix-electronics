<?php

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'shortcode' => env('MPESA_SHORTCODE', ''),
    'env' => env('MPESA_ENV', 'sandbox'),
    'callback_url' => env('MPESA_CALLBACK_URL', ''),
];
