<?php
// FILE: /app/models/AvatarStyle.php

class AvatarStyle extends Model {
    protected $table = 'avatar_styles';

    public function getAll($tenantId = null) {
        if ($tenantId === null) {
            return $this->db->fetchAll(
                "SELECT * FROM avatar_styles WHERE is_active = 1 AND tenant_id IS NULL ORDER BY name ASC"
            );
        }

        return $this->db->fetchAll(
            "SELECT * FROM avatar_styles
             WHERE is_active = 1 AND (tenant_id IS NULL OR tenant_id = ?)
             ORDER BY name ASC",
            [$tenantId]
        );
    }

    public function getBySlug($slug, $tenantId = null) {
        if ($tenantId === null) {
            return $this->db->fetch(
                "SELECT * FROM avatar_styles WHERE slug = ? AND tenant_id IS NULL LIMIT 1",
                [$slug]
            );
        }

        return $this->db->fetch(
            "SELECT * FROM avatar_styles
             WHERE slug = ? AND (tenant_id IS NULL OR tenant_id = ?)
             LIMIT 1",
            [$slug, $tenantId]
        );
    }

    public function createStyle($data) {
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = SlugHelper::unique($data['name'], 'avatar_styles');
        }

        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }

        if (!isset($data['credits_cost'])) {
            $data['credits_cost'] = 10;
        }

        return $this->create($data);
    }

    public function getGlobalStyles() {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_styles WHERE tenant_id IS NULL AND is_active = 1 ORDER BY name ASC"
        );
    }

    public function getTenantStyles($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_styles WHERE tenant_id = ? ORDER BY name ASC",
            [$tenantId]
        );
    }
}
