<?php
// FILE: /app/controllers/BillingController.php

class BillingController extends Controller {
    public function index() {
        if (!Auth::hasAnyRole(['tenant_admin', 'platform_admin'])) {
            Session::flash('error', 'You do not have permission to access billing');
            Response::redirect('/dashboard');
        }

        $tenantId = Auth::tenantId();

        $tenantModel = new Tenant();
        $subscription = $tenantModel->getActiveSubscription($tenantId);

        $usageHelper = new UsageHelper();
        $credits = $usageHelper->getCurrentMonthCredits($tenantId);
        $stats = $usageHelper->getUsageStats($tenantId);

        $creditModel = new TenantCredit();
        $creditHistory = $creditModel->getHistory($tenantId, 12);

        $planModel = new Plan();
        $plans = $planModel->getAllActive();

        $apiKeyModel = new TenantApiKey();
        $apiKeys = $apiKeyModel->getByTenant($tenantId);

        return $this->view('billing/index', [
            'title' => 'Billing & Usage',
            'subscription' => $subscription,
            'credits' => $credits,
            'stats' => $stats,
            'creditHistory' => $creditHistory,
            'plans' => $plans,
            'apiKeys' => $apiKeys,
        ]);
    }

    public function generateApiKey() {
        if (!Auth::hasAnyRole(['tenant_admin', 'platform_admin'])) {
            Response::json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        CSRF::validate();

        $tenantId = Auth::tenantId();
        $label = Request::post('label', 'API Key');

        $apiKeyModel = new TenantApiKey();
        $keyId = $apiKeyModel->generateApiKey($tenantId, $label);

        $key = $apiKeyModel->find($keyId);

        Session::flash('success', 'API key generated successfully');
        Response::redirect('/billing#api-keys');
    }

    public function revokeApiKey($keyId) {
        if (!Auth::hasAnyRole(['tenant_admin', 'platform_admin'])) {
            Session::flash('error', 'Unauthorized');
            Response::redirect('/dashboard');
        }

        CSRF::validate();

        $tenantId = Auth::tenantId();

        $apiKeyModel = new TenantApiKey();
        $key = $apiKeyModel->find($keyId);

        if (!$key || $key['tenant_id'] != $tenantId) {
            Session::flash('error', 'API key not found');
            Response::redirect('/billing');
        }

        $apiKeyModel->update($keyId, ['is_active' => 0]);

        Session::flash('success', 'API key revoked successfully');
        Response::redirect('/billing#api-keys');
    }
}
