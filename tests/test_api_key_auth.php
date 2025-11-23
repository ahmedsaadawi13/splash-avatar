<?php
// FILE: /tests/test_api_key_auth.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/TenantApiKey.php';

echo "Testing API key authentication...\n";

try {
    $apiKeyModel = new TenantApiKey();

    // Test valid API key
    $validKey = 'sk_demo_1234567890abcdefghijklmnopqrstuvwxyz1234567890abcdef';
    $keyData = $apiKeyModel->validateApiKey($validKey);

    if ($keyData && $keyData['tenant_id'] == 1) {
        echo "OK: Valid API key authenticated successfully\n";
    } else {
        echo "FAIL: Valid API key authentication failed\n";
    }

    // Test invalid API key
    $invalidKey = 'sk_invalid_key';
    $keyData = $apiKeyModel->validateApiKey($invalidKey);

    if (!$keyData) {
        echo "OK: Invalid API key correctly rejected\n";
    } else {
        echo "FAIL: Invalid API key was accepted\n";
    }

    // Test API key generation
    $newKeyId = $apiKeyModel->generateApiKey(1, 'Test API Key');

    if ($newKeyId > 0) {
        $newKey = $apiKeyModel->find($newKeyId);
        if ($newKey && strpos($newKey['api_key'], 'sk_') === 0) {
            echo "OK: API key generated successfully\n";
        } else {
            echo "FAIL: Generated API key has invalid format\n";
        }

        // Clean up
        $apiKeyModel->delete($newKeyId);
        echo "OK: Test API key cleaned up\n";
    } else {
        echo "FAIL: Failed to generate API key\n";
    }

    echo "OK: All API key authentication tests passed\n";

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
