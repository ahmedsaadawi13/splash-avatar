<?php
// FILE: /app/controllers/AvatarJobController.php

class AvatarJobController extends Controller {
    public function index() {
        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $jobModel = new AvatarJob();

        if (Auth::isEndUser()) {
            $jobs = $jobModel->getByUser($userId, $tenantId);
        } else {
            $jobs = $jobModel->getByTenant($tenantId);
        }

        return $this->view('jobs/index', [
            'title' => 'Avatar Jobs',
            'jobs' => $jobs,
        ]);
    }

    public function showCreate() {
        $tenantId = Auth::tenantId();

        $faceModel = new FaceUpload();
        $faces = $faceModel->getValidated($tenantId);

        $styleModel = new AvatarStyle();
        $styles = $styleModel->getAll($tenantId);

        $usageHelper = new UsageHelper();
        $credits = $usageHelper->getCurrentMonthCredits($tenantId);

        return $this->view('jobs/create', [
            'title' => 'Create Avatar Job',
            'faces' => $faces,
            'styles' => $styles,
            'credits' => $credits,
        ]);
    }

    public function create() {
        CSRF::validate();

        $tenantId = Auth::tenantId();
        $userId = Auth::id();

        $title = Request::post('title', 'Avatar Job');
        $description = Request::post('description', '');
        $faceIds = Request::post('face_ids', []);
        $styleIds = Request::post('style_ids', []);

        if (empty($faceIds) || empty($styleIds)) {
            Session::flash('error', 'Please select at least one face and one style');
            Response::redirect('/jobs/create');
        }

        $faceIds = is_array($faceIds) ? $faceIds : [$faceIds];
        $styleIds = is_array($styleIds) ? $styleIds : [$styleIds];

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
        if (!$usageHelper->hasCredits($tenantId, $totalCreditsCost)) {
            Session::flash('error', 'Insufficient credits. Required: ' . $totalCreditsCost);
            Response::redirect('/jobs/create');
        }

        $totalAvatarsRequested = count($faceIds) * count($styleIds);

        try {
            $this->db->beginTransaction();

            $jobModel = new AvatarJob();
            $jobId = $jobModel->createJob([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'credits_cost' => $totalCreditsCost,
                'total_avatars_requested' => $totalAvatarsRequested,
                'total_avatars_generated' => 0,
                'status' => 'pending',
            ]);

            $jobModel->addFaces($jobId, $faceIds, $tenantId);
            $jobModel->addStyles($jobId, $styleIds, $tenantId);

            $this->db->commit();

            Session::flash('success', 'Avatar job created successfully. Job ID: ' . $jobId);
            Response::redirect('/jobs/run/' . $jobId);

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Job creation error: ' . $e->getMessage());
            Session::flash('error', 'Failed to create job. Please try again.');
            Response::redirect('/jobs/create');
        }
    }

    public function run($jobId) {
        $tenantId = Auth::tenantId();

        $jobModel = new AvatarJob();
        $job = $jobModel->find($jobId);

        if (!$job || $job['tenant_id'] != $tenantId) {
            Session::flash('error', 'Job not found');
            Response::redirect('/jobs');
        }

        if ($job['status'] !== 'pending') {
            Session::flash('info', 'Job already processed');
            Response::redirect('/jobs/view/' . $jobId);
        }

        try {
            $pipeline = new AvatarPipelineHelper();
            $result = $pipeline->runJob($jobId);

            if ($result['success']) {
                Session::flash('success', 'Avatar job completed successfully! Generated ' . $result['avatars_generated'] . ' avatars.');
            } else {
                Session::flash('error', 'Job failed: ' . $result['error']);
            }

        } catch (Exception $e) {
            error_log('Job execution error: ' . $e->getMessage());
            Session::flash('error', 'Failed to run job: ' . $e->getMessage());
        }

        Response::redirect('/jobs/view/' . $jobId);
    }

    public function view($jobId) {
        $tenantId = Auth::tenantId();

        $jobModel = new AvatarJob();
        $job = $jobModel->getWithDetails($jobId);

        if (!$job || $job['tenant_id'] != $tenantId) {
            Session::flash('error', 'Job not found');
            Response::redirect('/jobs');
        }

        $avatarModel = new Avatar();
        $avatars = $avatarModel->getByJob($jobId);

        $packModel = new AvatarPack();
        $pack = $packModel->getByJob($jobId);

        return $this->view('jobs/view', [
            'title' => 'Job: ' . $job['title'],
            'job' => $job,
            'avatars' => $avatars,
            'pack' => $pack,
        ]);
    }
}
