<?php
// FILE: /app/helpers/UsageHelper.php

class UsageHelper {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getCurrentMonthCredits($tenantId) {
        $currentMonth = date('Y-m');

        $credits = $this->db->fetch(
            "SELECT * FROM tenant_credits WHERE tenant_id = ? AND month = ? LIMIT 1",
            [$tenantId, $currentMonth]
        );

        if (!$credits) {
            $credits = $this->initializeMonthCredits($tenantId);
        }

        return $credits;
    }

    private function initializeMonthCredits($tenantId) {
        $currentMonth = date('Y-m');

        $subscription = $this->db->fetch(
            "SELECT ts.*, p.monthly_credits
             FROM tenant_subscriptions ts
             JOIN plans p ON ts.plan_id = p.id
             WHERE ts.tenant_id = ? AND ts.status = 'active'
             LIMIT 1",
            [$tenantId]
        );

        $allocatedCredits = $subscription ? $subscription['monthly_credits'] : 0;

        $this->db->execute(
            "INSERT INTO tenant_credits (tenant_id, month, credits_allocated, credits_used, credits_remaining, created_at, updated_at)
             VALUES (?, ?, ?, 0, ?, NOW(), NOW())",
            [$tenantId, $currentMonth, $allocatedCredits, $allocatedCredits]
        );

        return $this->db->fetch(
            "SELECT * FROM tenant_credits WHERE tenant_id = ? AND month = ? LIMIT 1",
            [$tenantId, $currentMonth]
        );
    }

    public function hasCredits($tenantId, $requiredCredits) {
        $credits = $this->getCurrentMonthCredits($tenantId);
        return $credits['credits_remaining'] >= $requiredCredits;
    }

    public function deductCredits($tenantId, $creditsToDeduct, $description = '') {
        $currentMonth = date('Y-m');

        $credits = $this->getCurrentMonthCredits($tenantId);

        if ($credits['credits_remaining'] < $creditsToDeduct) {
            throw new Exception('Insufficient credits');
        }

        $this->db->beginTransaction();

        try {
            $this->db->execute(
                "UPDATE tenant_credits
                 SET credits_used = credits_used + ?,
                     credits_remaining = credits_remaining - ?,
                     updated_at = NOW()
                 WHERE tenant_id = ? AND month = ?",
                [$creditsToDeduct, $creditsToDeduct, $tenantId, $currentMonth]
            );

            $this->logUsage($tenantId, 'credits_deducted', $creditsToDeduct, $description);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function addCredits($tenantId, $creditsToAdd, $description = '') {
        $currentMonth = date('Y-m');

        $this->db->execute(
            "UPDATE tenant_credits
             SET credits_allocated = credits_allocated + ?,
                 credits_remaining = credits_remaining + ?,
                 updated_at = NOW()
             WHERE tenant_id = ? AND month = ?",
            [$creditsToAdd, $creditsToAdd, $tenantId, $currentMonth]
        );

        $this->logUsage($tenantId, 'credits_added', $creditsToAdd, $description);
    }

    public function getStorageUsage($tenantId) {
        $uploadDir = __DIR__ . '/../../storage/uploads/';
        $tenantDir = $uploadDir . 'faces/' . $tenantId;
        $avatarDir = $uploadDir . 'avatars/' . $tenantId;

        $totalSize = 0;

        if (is_dir($tenantDir)) {
            $totalSize += $this->getDirectorySize($tenantDir);
        }

        if (is_dir($avatarDir)) {
            $totalSize += $this->getDirectorySize($avatarDir);
        }

        return $totalSize;
    }

    private function getDirectorySize($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    public function logUsage($tenantId, $action, $quantity, $description = '') {
        $this->db->execute(
            "INSERT INTO tenant_usage (tenant_id, action, quantity, description, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [$tenantId, $action, $quantity, $description]
        );
    }

    public function getUsageStats($tenantId, $startDate = null, $endDate = null) {
        if ($startDate === null) {
            $startDate = date('Y-m-01');
        }
        if ($endDate === null) {
            $endDate = date('Y-m-t');
        }

        $stats = [
            'total_jobs' => 0,
            'total_avatars' => 0,
            'credits_used' => 0,
            'storage_mb' => 0,
        ];

        $jobCount = $this->db->fetch(
            "SELECT COUNT(*) as count FROM avatar_jobs
             WHERE tenant_id = ? AND created_at BETWEEN ? AND ?",
            [$tenantId, $startDate, $endDate]
        );
        $stats['total_jobs'] = $jobCount['count'];

        $avatarCount = $this->db->fetch(
            "SELECT COUNT(*) as count FROM avatars
             WHERE tenant_id = ? AND created_at BETWEEN ? AND ?",
            [$tenantId, $startDate, $endDate]
        );
        $stats['total_avatars'] = $avatarCount['count'];

        $credits = $this->getCurrentMonthCredits($tenantId);
        $stats['credits_used'] = $credits['credits_used'];

        $stats['storage_mb'] = round($this->getStorageUsage($tenantId) / 1048576, 2);

        return $stats;
    }
}
