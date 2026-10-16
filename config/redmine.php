<?php

declare(strict_types=1);

return [
    'base_url' => env('REDMINE_BASE_URL', ''),
    'api_key' => env('REDMINE_API_KEY', ''),
    'username' => env('REDMINE_USERNAME', ''),
    'password' => env('REDMINE_PASSWORD', ''),
    'default_user_id' => env('REDMINE_DEFAULT_USER_ID'),
];
