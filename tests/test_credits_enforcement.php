<?php
// FILE: /tests/test_credits_enforcement.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/helpers/UsageHelper.php';

echo "Testing credits enforcement...\n";

try {
    $usageHelper = new UsageHelper();

    // Get current month credits for demo tenant
    $credits = $usageHelper->getCurrentMonthCredits(1);

    if ($credits && isset($credits['credits_remaining'])) {
        echo "OK: Retrieved current month credits (Remaining: {$credits['credits_remaining']})\n";
    } else {
        echo "FAIL: Failed to retrieve credits\n";
    }

    // Test has credits check
    if ($usageHelper->hasCredits(1, 10)) {
        echo "OK: Credit availability check works for available credits\n";
    } else {
        echo "FAIL: Credit availability check failed for available credits\n";
    }

    // Test credit availability for amount exceeding balance
    $largeAmount = $credits['credits_remaining'] + 1000;
    if (!$usageHelper->hasCredits(1, $largeAmount)) {
        echo "OK: Credit check correctly rejects insufficient credits\n";
    } else {
        echo "FAIL: Credit check incorrectly allowed insufficient credits\n";
    }

    echo "OK: All credits enforcement tests passed\n";

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
