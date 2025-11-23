<?php
// FILE: /app/models/TenantUsage.php

class TenantUsage extends Model {
    protected $table = 'tenant_usage';

    public function getByTenant($tenantId, $startDate = null, $endDate = null) {
        if ($startDate === null) {
            $startDate = date('Y-m-01');
        }
        if ($endDate === null) {
            $endDate = date('Y-m-t');
        }

        return $this->db->fetchAll(
            "SELECT * FROM tenant_usage
             WHERE tenant_id = ? AND created_at BETWEEN ? AND ?
             ORDER BY created_at DESC",
            [$tenantId, $startDate, $endDate]
        );
    }

    public function logUsage($tenantId, $action, $quantity, $description = '') {
        return $this->create([
            'tenant_id' => $tenantId,
            'action' => $action,
            'quantity' => $quantity,
            'description' => $description,
        ]);
    }
}
