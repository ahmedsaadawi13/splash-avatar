<?php
// FILE: /app/middleware/TenantMiddleware.php

class TenantMiddleware {
    public function handle() {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to continue');
            Response::redirect('/login');
        }

        $tenantId = Auth::tenantId();

        if (!$tenantId) {
            Session::flash('error', 'No tenant associated with your account');
            Auth::logout();
            Response::redirect('/login');
        }

        $db = Database::getInstance();
        $tenant = $db->fetch("SELECT * FROM tenants WHERE id = ? AND status = 'active' LIMIT 1", [$tenantId]);

        if (!$tenant) {
            Session::flash('error', 'Your tenant account is inactive or not found');
            Auth::logout();
            Response::redirect('/login');
        }
    }
}
