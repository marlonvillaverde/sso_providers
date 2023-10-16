<?php

return [
    'issuer' => env('APP_URL'),
    'callback_url' => env('APP_URL').'/api/sp/callback',
    'app_url' => env('APP_URL'),
    'passport_client_id' => env('SSO_PASSPORT_CLIENT_ID'),
    'passport_client_secret' => env('SSO_PASSPORT_CLIENT_SECRET'),
];
