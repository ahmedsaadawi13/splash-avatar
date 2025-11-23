<?php
// FILE: /config/config.php

// Load environment variables from .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'SplashAvatar',
    'app_env' => $_ENV['APP_ENV'] ?? 'production',
    'app_debug' => isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true',

    'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
    'db_name' => $_ENV['DB_NAME'] ?? 'splashavatar',
    'db_user' => $_ENV['DB_USER'] ?? 'root',
    'db_pass' => $_ENV['DB_PASS'] ?? '',

    'app_url' => $_ENV['APP_URL'] ?? 'http://localhost',

    'timezone' => 'UTC',
];
