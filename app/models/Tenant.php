<?php
// FILE: /app/models/Tenant.php

class Tenant extends Model {
    protected $table = 'tenants';

    public function findBySlug($slug) {
        return $this->findBy('slug', $slug);
    }

    public function createTenant($data) {
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = SlugHelper::unique($data['name'], 'tenants');
        }

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $this->create($data);
    }

    public function getActiveSubscription($tenantId) {
        return $this->db->fetch(
            "SELECT ts.*, p.name as plan_name, p.monthly_credits, p.max_users, p.max_storage_mb
             FROM tenant_subscriptions ts
             JOIN plans p ON ts.plan_id = p.id
             WHERE ts.tenant_id = ? AND ts.status = 'active'
             ORDER BY ts.created_at DESC
             LIMIT 1",
            [$tenantId]
        );
    }

    public function getAllActive() {
        return $this->db->fetchAll(
            "SELECT * FROM tenants WHERE status = 'active' ORDER BY created_at DESC"
        );
    }
}
