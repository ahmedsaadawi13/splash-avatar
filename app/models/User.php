<?php
// FILE: /app/models/User.php

class User extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }

    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $this->create($data);
    }

    public function updatePassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->execute(
            "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [$passwordHash, $userId]
        );
    }

    public function getAllByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM users WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function countByTenant($tenantId) {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE tenant_id = ?",
            [$tenantId]
        );
        return (int) $result['count'];
    }
}
