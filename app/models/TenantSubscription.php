<?php
// FILE: /app/models/TenantSubscription.php

class TenantSubscription extends Model {
    protected $table = 'tenant_subscriptions';

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT ts.*, p.name as plan_name
             FROM tenant_subscriptions ts
             JOIN plans p ON ts.plan_id = p.id
             WHERE ts.tenant_id = ?
             ORDER BY ts.created_at DESC",
            [$tenantId]
        );
    }

    public function getActive($tenantId) {
        return $this->db->fetch(
            "SELECT ts.*, p.*
             FROM tenant_subscriptions ts
             JOIN plans p ON ts.plan_id = p.id
             WHERE ts.tenant_id = ? AND ts.status = 'active'
             LIMIT 1",
            [$tenantId]
        );
    }

    public function createSubscription($tenantId, $planId, $status = 'active') {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 year'));
        $renewalDate = date('Y-m-d', strtotime('+1 month'));

        return $this->create([
            'tenant_id' => $tenantId,
            'plan_id' => $planId,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'renewal_date' => $renewalDate,
        ]);
    }
}
