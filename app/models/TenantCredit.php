<?php
// FILE: /app/models/TenantCredit.php

class TenantCredit extends Model {
    protected $table = 'tenant_credits';

    public function getCurrentMonth($tenantId) {
        $currentMonth = date('Y-m');
        return $this->db->fetch(
            "SELECT * FROM tenant_credits WHERE tenant_id = ? AND month = ? LIMIT 1",
            [$tenantId, $currentMonth]
        );
    }

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM tenant_credits WHERE tenant_id = ? ORDER BY month DESC",
            [$tenantId]
        );
    }

    public function getHistory($tenantId, $limit = 12) {
        return $this->db->fetchAll(
            "SELECT * FROM tenant_credits WHERE tenant_id = ? ORDER BY month DESC LIMIT ?",
            [$tenantId, $limit]
        );
    }
}
