<?php
// FILE: /tests/test_db_connection.php

require_once __DIR__ . '/../app/core/Database.php';

echo "Testing database connection...\n";

try {
    $db = Database::getInstance();
    $result = $db->fetch("SELECT 1 as test");

    if ($result && $result['test'] == 1) {
        echo "OK: Database connection successful\n";
    } else {
        echo "FAIL: Database query returned unexpected result\n";
    }
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
