<?php
// FILE: /tests/test_create_avatar_job.php

require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/models/AvatarJob.php';

echo "Testing avatar job creation...\n";

try {
    $db = Database::getInstance();

    $jobModel = new AvatarJob();

    $jobId = $jobModel->createJob([
        'tenant_id' => 1,
        'user_id' => 3,
        'title' => 'Test Job',
        'description' => 'Test avatar job',
        'credits_cost' => 30,
        'total_avatars_requested' => 6,
        'total_avatars_generated' => 0,
        'status' => 'pending',
    ]);

    if ($jobId > 0) {
        echo "OK: Avatar job created successfully (ID: {$jobId})\n";

        // Verify the job exists
        $job = $jobModel->find($jobId);
        if ($job && $job['title'] === 'Test Job') {
            echo "OK: Job data verified correctly\n";
        } else {
            echo "FAIL: Job data verification failed\n";
        }

        // Clean up
        $jobModel->delete($jobId);
        echo "OK: Test job cleaned up\n";

    } else {
        echo "FAIL: Failed to create avatar job\n";
    }

} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}
