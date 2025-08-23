<?php

return [
    'url' => env('PAYGINE_URL', 'https://test.paygine.com/webapi/'),
    'sector' => env('PAYGINE_SECTOR'),
    'password' => env('PAYGINE_PASSWORD'),
    'success_url' => env('PAYGINE_SUCCESS_URL', 'https://localhost:8083/payment/success'),
    'fail_url' => env('PAYGINE_FAIL_URL', 'https://localhost:8083/payment/fail'),
    'notify_url' => env('PAYGINE_NOTIFY_URL', 'https://localhost:8083/payment/notify'),
];
