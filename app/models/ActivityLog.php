<?php
// FILE: /app/models/ActivityLog.php

class ActivityLog extends Model {
    protected $table = 'activity_logs';

    public function log($tenantId, $userId, $action, $description, $metadata = null) {
        $data = [
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        return $this->create($data);
    }

    public function getByTenant($tenantId, $limit = 100) {
        return $this->db->fetchAll(
            "SELECT al.*, u.name as user_name, u.email as user_email
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE al.tenant_id = ?
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$tenantId, $limit]
        );
    }

    public function getByUser($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }
}
