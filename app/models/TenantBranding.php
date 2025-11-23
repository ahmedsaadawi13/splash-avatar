<?php
// FILE: /app/models/TenantBranding.php

class TenantBranding extends Model {
    protected $table = 'tenant_branding';

    public function getByTenant($tenantId) {
        return $this->db->fetch(
            "SELECT * FROM tenant_branding WHERE tenant_id = ? LIMIT 1",
            [$tenantId]
        );
    }

    public function upsert($tenantId, $data) {
        $existing = $this->getByTenant($tenantId);

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['tenant_id'] = $tenantId;
            return $this->create($data);
        }
    }
}
