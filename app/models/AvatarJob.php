<?php
// FILE: /app/models/AvatarJob.php

class AvatarJob extends Model {
    protected $table = 'avatar_jobs';

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_jobs WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function getByUser($userId, $tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM avatar_jobs WHERE user_id = ? AND tenant_id = ? ORDER BY created_at DESC",
            [$userId, $tenantId]
        );
    }

    public function createJob($data) {
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        if (!isset($data['total_avatars_generated'])) {
            $data['total_avatars_generated'] = 0;
        }

        return $this->create($data);
    }

    public function getWithDetails($jobId) {
        $job = $this->find($jobId);

        if (!$job) {
            return null;
        }

        $job['faces'] = $this->db->fetchAll(
            "SELECT fu.* FROM face_uploads fu
             JOIN avatar_job_faces ajf ON ajf.face_upload_id = fu.id
             WHERE ajf.job_id = ?",
            [$jobId]
        );

        $job['styles'] = $this->db->fetchAll(
            "SELECT ast.* FROM avatar_styles ast
             JOIN avatar_job_styles ajs ON ajs.style_id = ast.id
             WHERE ajs.job_id = ?",
            [$jobId]
        );

        return $job;
    }

    public function addFaces($jobId, $faceIds, $tenantId) {
        foreach ($faceIds as $faceId) {
            $this->db->execute(
                "INSERT INTO avatar_job_faces (tenant_id, job_id, face_upload_id, created_at)
                 VALUES (?, ?, ?, NOW())",
                [$tenantId, $jobId, $faceId]
            );
        }
    }

    public function addStyles($jobId, $styleIds, $tenantId) {
        foreach ($styleIds as $styleId) {
            $this->db->execute(
                "INSERT INTO avatar_job_styles (tenant_id, job_id, style_id, created_at)
                 VALUES (?, ?, ?, NOW())",
                [$tenantId, $jobId, $styleId]
            );
        }
    }
}
