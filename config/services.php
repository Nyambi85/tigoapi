<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'TIGO_AGR_MON' => [
        'API_SOURCE' => env('API_SOURCE'),
        'BASE_URL' => env('TIGO_API_BASE_URL'),
        'BASE_PAYOUT_URL' => env('TIGO_API_BASE_PAYOUT_URL'),
        'API_CALL_BACK_B2C' => env('TIGO_PAY_OUT_CALL_BACK_B2C'),
        'BILLER_MSISDN' => env('TIGO_BILLER_MSISDN'),
        'API_KEY' => env('TIGO_API_KEY'),
        'API_SERVICE_ID' => env('TIGO_API_SERVICE_ID'),
        'API_CLIENT_SECRET' => env('TIGO_API_SECRET_KEY'),
        'API_AUTH_END_POINT' => env('TIGO_API_AUTH_ENDPOINT'),
        'API_COLLECTION_PUSH_API_ENDPOINT' => env('TIGO_API_PUSH_API_ENDPOINT'),
        'API_REQ_CALL_BACK' => env('API_REQ_CALL_BACK'),
        'API_PASS' => env('API_PASSWORD'),
        'API_PAYOUT_METHOD' => env('API_PAY_OUT_CALL_BACK_METHOD'),
        'API_PRODUCT' => env('API_PRODUCT'),
        'API_PUCLIC_KEY' => env('TIGO_PUBLIC_KEY'),
        'API_SECRET_KEY' => env('TIGO_SECRET')
    ],

];
