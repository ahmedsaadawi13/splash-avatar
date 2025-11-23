<?php
// FILE: /app/models/Avatar.php

class Avatar extends Model {
    protected $table = 'avatars';

    public function getByJob($jobId) {
        return $this->db->fetchAll(
            "SELECT a.*, ast.name as style_name
             FROM avatars a
             JOIN avatar_styles ast ON a.style_id = ast.id
             WHERE a.job_id = ?
             ORDER BY a.created_at DESC",
            [$jobId]
        );
    }

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatars WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function incrementDownloadCount($avatarId) {
        return $this->db->execute(
            "UPDATE avatars SET download_count = download_count + 1, updated_at = NOW() WHERE id = ?",
            [$avatarId]
        );
    }

    public function getWithDetails($avatarId) {
        return $this->db->fetch(
            "SELECT a.*, ast.name as style_name, fu.title as face_title
             FROM avatars a
             JOIN avatar_styles ast ON a.style_id = ast.id
             JOIN face_uploads fu ON a.face_upload_id = fu.id
             WHERE a.id = ?
             LIMIT 1",
            [$avatarId]
        );
    }
}
