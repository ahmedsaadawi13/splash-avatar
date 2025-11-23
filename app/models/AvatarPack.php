<?php
// FILE: /app/models/AvatarPack.php

class AvatarPack extends Model {
    protected $table = 'avatar_packs';

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_packs WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function getByUser($userId, $tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_packs WHERE user_id = ? AND tenant_id = ? ORDER BY created_at DESC",
            [$userId, $tenantId]
        );
    }

    public function getByJob($jobId) {
        return $this->db->fetch(
            "SELECT * FROM avatar_packs WHERE job_id = ? LIMIT 1",
            [$jobId]
        );
    }

    public function getWithAvatars($packId) {
        $pack = $this->find($packId);

        if (!$pack) {
            return null;
        }

        $avatarModel = new Avatar();
        $pack['avatars'] = $avatarModel->getByJob($pack['job_id']);

        return $pack;
    }
}
