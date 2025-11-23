<?php
// FILE: /tests/test_auth_login.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/Auth.php';

echo "Testing authentication...\n";

try {
    // Test valid login
    $result = Auth::attempt('demo@demo.com', 'demo123');

    if ($result) {
        echo "OK: Authentication successful for valid credentials\n";
        Auth::logout();
    } else {
        echo "FAIL: Authentication failed for valid credentials\n";
    }

    // Test invalid login
    $result = Auth::attempt('demo@demo.com', 'wrongpassword');

    if (!$result) {
        echo "OK: Authentication correctly rejected invalid credentials\n";
    } else {
        echo "FAIL: Authentication accepted invalid credentials\n";
    }

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
