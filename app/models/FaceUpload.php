<?php
// FILE: /app/models/FaceUpload.php

class FaceUpload extends Model {
    protected $table = 'face_uploads';

    public function getByTenant($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM face_uploads WHERE tenant_id = ? ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function getByUser($userId, $tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM face_uploads WHERE user_id = ? AND tenant_id = ? ORDER BY created_at DESC",
            [$userId, $tenantId]
        );
    }

    public function getValidated($tenantId) {
        return $this->db->fetchAll(
            "SELECT * FROM face_uploads
             WHERE tenant_id = ? AND status = 'validated' AND face_detected = 1
             ORDER BY created_at DESC",
            [$tenantId]
        );
    }

    public function createUpload($data) {
        if (!isset($data['status'])) {
            $data['status'] = 'uploaded';
        }

        if (!isset($data['face_detected'])) {
            $data['face_detected'] = 0;
        }

        return $this->create($data);
    }

    public function validate($uploadId, $faceDetected = true) {
        $status = $faceDetected ? 'validated' : 'rejected';
        $rejectionReason = $faceDetected ? null : 'No face detected in image';

        return $this->db->execute(
            "UPDATE face_uploads
             SET status = ?, face_detected = ?, rejection_reason = ?, updated_at = NOW()
             WHERE id = ?",
            [$status, $faceDetected ? 1 : 0, $rejectionReason, $uploadId]
        );
    }
}
