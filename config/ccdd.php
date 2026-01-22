<?php

return [
    'app_url' => env('APP_URL', 'https://app.example.com'),
    'order_link_ttl_minutes' => 60,
    'webhook_timeout' => 10,
    'webhook_max_attempts' => 8,
    'retry_1_days' => 3,
    'retry_2_days' => 7,
    'max_retries' => 2,
    'advance_notice_days' => 3,
    'bacs_storage_path' => 'bacs',
];
