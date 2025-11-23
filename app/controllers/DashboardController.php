<?php
// FILE: /app/controllers/DashboardController.php

class DashboardController extends Controller {
    public function index() {
        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $usageHelper = new UsageHelper();
        $stats = $usageHelper->getUsageStats($tenantId);

        $credits = $usageHelper->getCurrentMonthCredits($tenantId);

        $jobModel = new AvatarJob();
        $recentJobs = $this->db->fetchAll(
            "SELECT * FROM avatar_jobs WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 5",
            [$tenantId]
        );

        $packModel = new AvatarPack();
        $recentPacks = $this->db->fetchAll(
            "SELECT * FROM avatar_packs WHERE tenant_id = ? ORDER BY created_at DESC LIMIT 5",
            [$tenantId]
        );

        $tenantModel = new Tenant();
        $subscription = $tenantModel->getActiveSubscription($tenantId);

        return $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'credits' => $credits,
            'subscription' => $subscription,
            'recentJobs' => $recentJobs,
            'recentPacks' => $recentPacks,
        ]);
    }
}
