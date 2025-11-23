<?php
// FILE: /app/controllers/ApiController.php

class ApiController extends Controller {
    private $apiKey;
    private $tenant;

    public function __construct() {
        parent::__construct();
        $this->authenticateApiKey();
    }

    private function authenticateApiKey() {
        $apiKey = Request::header('X-API-KEY');

        if (!$apiKey) {
            Response::json([
                'status' => 'error',
                'message' => 'API key required. Please provide X-API-KEY header.',
            ], 401);
        }

        $apiKeyModel = new TenantApiKey();
        $keyData = $apiKeyModel->validateApiKey($apiKey);

        if (!$keyData) {
            Response::json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key',
            ], 401);
        }

        $this->apiKey = $keyData;
        $this->tenant = $keyData['tenant_id'];

        $this->checkRateLimit();
    }

    private function checkRateLimit() {
        // Simple rate limiting - in production, use Redis or similar
        // For now, just a placeholder
    }

    public function createJob() {
        $data = Request::getJson();

        if (!isset($data['style_ids']) || !isset($data['face_image_urls'])) {
            Response::json([
                'status' => 'error',
                'message' => 'Missing required fields: style_ids, face_image_urls',
            ], 400);
        }

        $styleIds = $data['style_ids'];
        $faceImageUrls = $data['face_image_urls'];
        $userReference = isset($data['user_reference']) ? $data['user_reference'] : 'api_user';

        $faceIds = [];
        foreach ($faceImageUrls as $url) {
            $faceModel = new FaceUpload();
            $faceId = $faceModel->createUpload([
                'tenant_id' => $this->tenant,
                'user_id' => null,
                'title' => 'API Upload - ' . basename($url),
                'file_path' => 'api_simulated/' . uniqid() . '.jpg',
                'original_file_name' => basename($url),
                'mime_type' => 'image/jpeg',
                'size_bytes' => 0,
                'face_detected' => 1,
                'status' => 'validated',
            ]);
            $faceIds[] = $faceId;
        }

        $styleModel = new AvatarStyle();
        $totalCreditsCost = 0;
        foreach ($styleIds as $styleId) {
            $style = $styleModel->find($styleId);
            if ($style) {
                $totalCreditsCost += $style['credits_cost'];
            }
        }
        $totalCreditsCost *= count($faceIds);

        $usageHelper = new UsageHelper();
        if (!$usageHelper->hasCredits($this->tenant, $totalCreditsCost)) {
            Response::json([
                'status' => 'error',
                'message' => 'Insufficient credits',
                'data' => [
                    'required_credits' => $totalCreditsCost,
                ],
            ], 402);
        }

        $totalAvatarsRequested = count($faceIds) * count($styleIds);

        try {
            $this->db->beginTransaction();

            $jobModel = new AvatarJob();
            $jobId = $jobModel->createJob([
                'tenant_id' => $this->tenant,
                'user_id' => null,
                'title' => 'API Job - ' . date('Y-m-d H:i:s'),
                'description' => 'Created via API',
                'credits_cost' => $totalCreditsCost,
                'total_avatars_requested' => $totalAvatarsRequested,
                'total_avatars_generated' => 0,
                'status' => 'pending',
            ]);

            $jobModel->addFaces($jobId, $faceIds, $this->tenant);
            $jobModel->addStyles($jobId, $styleIds, $this->tenant);

            $this->db->commit();

            $pipeline = new AvatarPipelineHelper();
            $result = $pipeline->runJob($jobId);

            Response::json([
                'status' => 'success',
                'message' => 'Job created and processing started',
                'data' => [
                    'job_id' => $jobId,
                    'status' => 'processing',
                    'avatars_requested' => $totalAvatarsRequested,
                ],
            ], 201);

        } catch (Exception $e) {
            $this->db->rollBack();
            Response::json([
                'status' => 'error',
                'message' => 'Failed to create job: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getJobStatus($jobId) {
        $jobModel = new AvatarJob();
        $job = $jobModel->find($jobId);

        if (!$job || $job['tenant_id'] != $this->tenant) {
            Response::json([
                'status' => 'error',
                'message' => 'Job not found',
            ], 404);
        }

        Response::json([
            'status' => 'success',
            'data' => [
                'job_id' => $job['id'],
                'title' => $job['title'],
                'status' => $job['status'],
                'avatars_requested' => $job['total_avatars_requested'],
                'avatars_generated' => $job['total_avatars_generated'],
                'credits_cost' => $job['credits_cost'],
                'error_message' => $job['error_message'],
                'created_at' => $job['created_at'],
                'updated_at' => $job['updated_at'],
            ],
        ]);
    }

    public function getJobAvatars($jobId) {
        $jobModel = new AvatarJob();
        $job = $jobModel->find($jobId);

        if (!$job || $job['tenant_id'] != $this->tenant) {
            Response::json([
                'status' => 'error',
                'message' => 'Job not found',
            ], 404);
        }

        $avatarModel = new Avatar();
        $avatars = $avatarModel->getByJob($jobId);

        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        $avatarsData = array_map(function($avatar) use ($baseUrl) {
            return [
                'id' => $avatar['id'],
                'style_name' => $avatar['style_name'],
                'image_url' => $baseUrl . '/storage/uploads/' . $avatar['image_path'],
                'thumbnail_url' => $baseUrl . '/storage/uploads/' . $avatar['thumbnail_path'],
                'width' => $avatar['width'],
                'height' => $avatar['height'],
                'download_count' => $avatar['download_count'],
                'created_at' => $avatar['created_at'],
            ];
        }, $avatars);

        Response::json([
            'status' => 'success',
            'data' => [
                'job_id' => $jobId,
                'total_avatars' => count($avatarsData),
                'avatars' => $avatarsData,
            ],
        ]);
    }

    public function getPack($packId) {
        $packModel = new AvatarPack();
        $pack = $packModel->getWithAvatars($packId);

        if (!$pack || $pack['tenant_id'] != $this->tenant) {
            Response::json([
                'status' => 'error',
                'message' => 'Pack not found',
            ], 404);
        }

        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        $avatarsData = array_map(function($avatar) use ($baseUrl) {
            return [
                'id' => $avatar['id'],
                'image_url' => $baseUrl . '/storage/uploads/' . $avatar['image_path'],
                'thumbnail_url' => $baseUrl . '/storage/uploads/' . $avatar['thumbnail_path'],
            ];
        }, $pack['avatars']);

        Response::json([
            'status' => 'success',
            'data' => [
                'pack_id' => $pack['id'],
                'name' => $pack['name'],
                'description' => $pack['description'],
                'total_avatars' => $pack['total_avatars'],
                'avatars' => $avatarsData,
                'created_at' => $pack['created_at'],
            ],
        ]);
    }
}
