<?php

return [
    'baseUrl' => env('SHIPROCKET_BASE_URL', 'https://apiv2.delhivery.in/v1/external'),
    'userEmail' => env('SHIPROCKET_API_USER_EMAIL'),
    'userPassword' => env('SHIPROCKET_API_USER_PASSWORD'),
    'pickupPostcode' => env('SHIPROCKET_PICKUP_POSTCODE'),
];
