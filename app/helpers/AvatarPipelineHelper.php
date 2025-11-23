<?php
// FILE: /app/helpers/AvatarPipelineHelper.php

class AvatarPipelineHelper {
    private $db;
    private $uploadDir;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->uploadDir = __DIR__ . '/../../storage/uploads/';
    }

    public function runJob($jobId) {
        try {
            $job = $this->db->fetch("SELECT * FROM avatar_jobs WHERE id = ?", [$jobId]);

            if (!$job) {
                throw new Exception('Job not found');
            }

            if ($job['status'] !== 'pending') {
                throw new Exception('Job is not in pending status');
            }

            $usageHelper = new UsageHelper();

            if (!$usageHelper->hasCredits($job['tenant_id'], $job['credits_cost'])) {
                $this->db->execute(
                    "UPDATE avatar_jobs SET status = 'failed', error_message = 'Insufficient credits', updated_at = NOW() WHERE id = ?",
                    [$jobId]
                );
                throw new Exception('Insufficient credits to run job');
            }

            $this->db->execute(
                "UPDATE avatar_jobs SET status = 'processing', updated_at = NOW() WHERE id = ?",
                [$jobId]
            );

            $faces = $this->db->fetchAll(
                "SELECT fu.* FROM face_uploads fu
                 JOIN avatar_job_faces ajf ON ajf.face_upload_id = fu.id
                 WHERE ajf.job_id = ?",
                [$jobId]
            );

            $styles = $this->db->fetchAll(
                "SELECT ast.* FROM avatar_styles ast
                 JOIN avatar_job_styles ajs ON ajs.style_id = ast.id
                 WHERE ajs.job_id = ?",
                [$jobId]
            );

            if (empty($faces) || empty($styles)) {
                throw new Exception('No faces or styles selected for this job');
            }

            $totalAvatarsGenerated = 0;

            foreach ($faces as $face) {
                foreach ($styles as $style) {
                    $avatarData = $this->generateAvatar($job, $face, $style);
                    $avatarId = $this->saveAvatar($avatarData);
                    $totalAvatarsGenerated++;
                }
            }

            $usageHelper->deductCredits($job['tenant_id'], $job['credits_cost'], "Avatar job #{$jobId}");

            $this->db->execute(
                "UPDATE avatar_jobs
                 SET status = 'completed',
                     total_avatars_generated = ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [$totalAvatarsGenerated, $jobId]
            );

            $this->createAvatarPack($job, $totalAvatarsGenerated);

            $usageHelper->logUsage($job['tenant_id'], 'avatars_generated', $totalAvatarsGenerated, "Job #{$jobId}");

            return [
                'success' => true,
                'job_id' => $jobId,
                'avatars_generated' => $totalAvatarsGenerated,
            ];

        } catch (Exception $e) {
            $this->db->execute(
                "UPDATE avatar_jobs SET status = 'failed', error_message = ?, updated_at = NOW() WHERE id = ?",
                [$e->getMessage(), $jobId]
            );

            error_log("Avatar job #{$jobId} failed: " . $e->getMessage());

            return [
                'success' => false,
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function generateAvatar($job, $face, $style) {
        $branding = $this->db->fetch(
            "SELECT * FROM tenant_branding WHERE tenant_id = ? LIMIT 1",
            [$job['tenant_id']]
        );

        $avatarDir = $this->uploadDir . 'avatars/' . $job['tenant_id'] . '/';
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }

        $uniqueId = uniqid('avatar_', true) . '_' . bin2hex(random_bytes(4));
        $avatarFileName = $uniqueId . '.jpg';
        $thumbnailFileName = $uniqueId . '_thumb.jpg';

        $avatarPath = $avatarDir . $avatarFileName;
        $thumbnailPath = $avatarDir . $thumbnailFileName;

        $this->simulateAvatarGeneration($face, $style, $avatarPath);

        ImageHelper::createThumbnail($avatarPath, $thumbnailPath, 300, 300);

        $isWatermarked = false;
        if ($branding && !empty($branding['watermark_text'])) {
            $watermarkedPath = $avatarDir . $uniqueId . '_wm.jpg';
            ImageHelper::addWatermark($avatarPath, $watermarkedPath, $branding['watermark_text'], $branding['watermark_opacity']);
            $avatarPath = $watermarkedPath;
            $isWatermarked = true;
        }

        $imageInfo = getimagesize($avatarPath);

        return [
            'tenant_id' => $job['tenant_id'],
            'job_id' => $job['id'],
            'style_id' => $style['id'],
            'face_upload_id' => $face['id'],
            'image_path' => str_replace($this->uploadDir, '', $avatarPath),
            'thumbnail_path' => str_replace($this->uploadDir, '', $thumbnailPath),
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'is_watermarked' => $isWatermarked ? 1 : 0,
            'download_count' => 0,
        ];
    }

    private function simulateAvatarGeneration($face, $style, $outputPath) {
        $placeholderPath = __DIR__ . '/../../public/assets/img/placeholder-avatar.jpg';

        if (file_exists($placeholderPath)) {
            copy($placeholderPath, $outputPath);
        } else {
            $width = 512;
            $height = 512;
            $image = imagecreatetruecolor($width, $height);

            $colors = [
                imagecolorallocate($image, 135, 206, 250),
                imagecolorallocate($image, 255, 182, 193),
                imagecolorallocate($image, 152, 251, 152),
                imagecolorallocate($image, 255, 218, 185),
                imagecolorallocate($image, 221, 160, 221),
            ];

            $bgColor = $colors[array_rand($colors)];
            imagefill($image, 0, 0, $bgColor);

            $textColor = imagecolorallocate($image, 255, 255, 255);
            $text = strtoupper(substr($style['name'], 0, 3));
            imagestring($image, 5, ($width / 2) - 20, ($height / 2) - 10, $text, $textColor);

            imagejpeg($image, $outputPath, 90);
            imagedestroy($image);
        }
    }

    private function saveAvatar($data) {
        $this->db->execute(
            "INSERT INTO avatars (tenant_id, job_id, style_id, face_upload_id, image_path, thumbnail_path, width, height, is_watermarked, download_count, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['tenant_id'],
                $data['job_id'],
                $data['style_id'],
                $data['face_upload_id'],
                $data['image_path'],
                $data['thumbnail_path'],
                $data['width'],
                $data['height'],
                $data['is_watermarked'],
                $data['download_count'],
            ]
        );

        return $this->db->lastInsertId();
    }

    private function createAvatarPack($job, $totalAvatars) {
        $avatars = $this->db->fetchAll(
            "SELECT * FROM avatars WHERE job_id = ? ORDER BY created_at ASC LIMIT 1",
            [$job['id']]
        );

        $coverImage = $avatars[0]['thumbnail_path'] ?? '';

        $packName = $job['title'] . ' - Avatar Pack';

        $this->db->execute(
            "INSERT INTO avatar_packs (tenant_id, job_id, user_id, name, description, total_avatars, cover_image_path, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $job['tenant_id'],
                $job['id'],
                $job['user_id'],
                $packName,
                $job['description'],
                $totalAvatars,
                $coverImage,
            ]
        );
    }

    public function getJobProgress($jobId) {
        $job = $this->db->fetch("SELECT * FROM avatar_jobs WHERE id = ?", [$jobId]);

        if (!$job) {
            return null;
        }

        $avatarsGenerated = $this->db->fetch(
            "SELECT COUNT(*) as count FROM avatars WHERE job_id = ?",
            [$jobId]
        );

        return [
            'job_id' => $jobId,
            'status' => $job['status'],
            'total_requested' => $job['total_avatars_requested'],
            'total_generated' => $avatarsGenerated['count'],
            'progress_percent' => $job['total_avatars_requested'] > 0
                ? round(($avatarsGenerated['count'] / $job['total_avatars_requested']) * 100, 2)
                : 0,
        ];
    }
}
