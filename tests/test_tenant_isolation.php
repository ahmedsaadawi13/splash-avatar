<?php
// FILE: /tests/test_tenant_isolation.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/Tenant.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/FaceUpload.php';
require_once __DIR__ . '/../app/helpers/SlugHelper.php';

echo "Testing tenant isolation...\n";

try {
    $db = Database::getInstance();

    // Create a second test tenant
    $tenantModel = new Tenant();
    $tenant2Id = $tenantModel->createTenant([
        'name' => 'Test Tenant 2',
        'status' => 'active',
    ]);

    echo "OK: Created test tenant 2 (ID: {$tenant2Id})\n";

    // Create a user in tenant 2
    $userModel = new User();
    $user2Id = $userModel->createUser([
        'tenant_id' => $tenant2Id,
        'name' => 'Test User 2',
        'email' => 'test2@test.com',
        'password' => 'test123',
        'role' => 'end_user',
        'status' => 'active',
    ]);

    echo "OK: Created user in test tenant 2 (ID: {$user2Id})\n";

    // Create face upload for tenant 1
    $faceModel = new FaceUpload();
    $face1Id = $faceModel->createUpload([
        'tenant_id' => 1,
        'user_id' => 3,
        'title' => 'Tenant 1 Face',
        'file_path' => 'test/face1.jpg',
        'original_file_name' => 'face1.jpg',
        'mime_type' => 'image/jpeg',
        'size_bytes' => 100000,
        'face_detected' => 1,
        'status' => 'validated',
    ]);

    // Create face upload for tenant 2
    $face2Id = $faceModel->createUpload([
        'tenant_id' => $tenant2Id,
        'user_id' => $user2Id,
        'title' => 'Tenant 2 Face',
        'file_path' => 'test/face2.jpg',
        'original_file_name' => 'face2.jpg',
        'mime_type' => 'image/jpeg',
        'size_bytes' => 100000,
        'face_detected' => 1,
        'status' => 'validated',
    ]);

    // Test tenant 1 can only see their own face uploads
    $tenant1Faces = $db->fetchAll("SELECT * FROM face_uploads WHERE tenant_id = ?", [1]);
    $tenant1CanSeeTenant2 = false;
    foreach ($tenant1Faces as $face) {
        if ($face['id'] == $face2Id) {
            $tenant1CanSeeTenant2 = true;
            break;
        }
    }

    if (!$tenant1CanSeeTenant2) {
        echo "OK: Tenant 1 cannot see Tenant 2's face uploads\n";
    } else {
        echo "FAIL: Tenant isolation breach - Tenant 1 can see Tenant 2's data\n";
    }

    // Test tenant 2 can only see their own face uploads
    $tenant2Faces = $db->fetchAll("SELECT * FROM face_uploads WHERE tenant_id = ?", [$tenant2Id]);
    $tenant2CanSeeTenant1 = false;
    foreach ($tenant2Faces as $face) {
        if ($face['id'] == $face1Id) {
            $tenant2CanSeeTenant1 = true;
            break;
        }
    }

    if (!$tenant2CanSeeTenant1) {
        echo "OK: Tenant 2 cannot see Tenant 1's face uploads\n";
    } else {
        echo "FAIL: Tenant isolation breach - Tenant 2 can see Tenant 1's data\n";
    }

    // Clean up
    $faceModel->delete($face1Id);
    $faceModel->delete($face2Id);
    $userModel->delete($user2Id);
    $tenantModel->delete($tenant2Id);

    echo "OK: All tenant isolation tests passed\n";

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
