<?php
// FILE: /app/models/TenantApiKey.php

class TenantApiKey extends Model {
    protected $table = 'tenant_api_keys';

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM tenant_api_keys WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function findByKey($apiKey) {
        return $this->findBy('api_key', $apiKey);
    }

    public function generateApiKey($tenantId, $label = 'Default API Key') {
        $apiKey = 'sk_' . bin2hex(random_bytes(32));

        return $this->create([
            'tenant_id' => $tenantId,
            'api_key' => $apiKey,
            'label' => $label,
            'is_active' => 1,
            'rate_limit_per_minute' => 60,
        ]);
    }

    public function validateApiKey($apiKey) {
        $key = $this->findByKey($apiKey);

        if (!$key) {
            return null;
        }

        if ($key['is_active'] != 1) {
            return null;
        }

        return $key;
    }
}
