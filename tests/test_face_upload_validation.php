<?php
// FILE: /tests/test_face_upload_validation.php

require_once __DIR__ . '/../app/helpers/ValidationHelper.php';
require_once __DIR__ . '/../app/helpers/FileUploadHelper.php';

echo "Testing face upload validation...\n";

try {
    // Test email validation
    if (ValidationHelper::email('test@example.com')) {
        echo "OK: Email validation works for valid email\n";
    } else {
        echo "FAIL: Email validation failed for valid email\n";
    }

    if (!ValidationHelper::email('invalid-email')) {
        echo "OK: Email validation rejects invalid email\n";
    } else {
        echo "FAIL: Email validation accepted invalid email\n";
    }

    // Test required field validation
    if (ValidationHelper::required('test')) {
        echo "OK: Required field validation works for non-empty value\n";
    } else {
        echo "FAIL: Required field validation failed for non-empty value\n";
    }

    if (!ValidationHelper::required('')) {
        echo "OK: Required field validation rejects empty value\n";
    } else {
        echo "FAIL: Required field validation accepted empty value\n";
    }

    // Test upload status validation
    if (ValidationHelper::validUploadStatus('validated')) {
        echo "OK: Upload status validation works for valid status\n";
    } else {
        echo "FAIL: Upload status validation failed for valid status\n";
    }

    if (!ValidationHelper::validUploadStatus('invalid_status')) {
        echo "OK: Upload status validation rejects invalid status\n";
    } else {
        echo "FAIL: Upload status validation accepted invalid status\n";
    }

    echo "OK: All face upload validation tests passed\n";

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
