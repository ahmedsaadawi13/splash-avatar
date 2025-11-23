<?php
// FILE: /public/index.php

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/logs/error.log');

// Autoloader for core classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/core/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/helpers/' . $class . '.php',
        __DIR__ . '/../app/middleware/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Start session
Session::start();

// Set timezone
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['timezone']);

// Initialize router
$router = new Router();

// Load routes
require __DIR__ . '/../config/routes.php';

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    error_log('Application error: ' . $e->getMessage());

    if ($config['app_debug']) {
        echo '<h1>Application Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>An error occurred. Please try again later.</p>';
    }
}
