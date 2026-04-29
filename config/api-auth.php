<?php

declare(strict_types=1);

return [
    'url' => env('AUTH_API_URL', 'https://auth.api.benefits.me'),
    'version' => env('AUTH_API_VERSION', '1.0.0'),
    'token_provider' => null,
];