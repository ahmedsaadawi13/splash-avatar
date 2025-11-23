<?php
// FILE: /app/controllers/AdminController.php

class AdminController extends Controller {
    public function __construct() {
        parent::__construct();

        if (!Auth::isPlatformAdmin()) {
            Session::flash('error', 'Platform admin access required');
            Response::redirect('/dashboard');
        }
    }

    public function index() {
        $tenantModel = new Tenant();
        $tenants = $tenantModel->all();

        $userModel = new User();
        $totalUsers = $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];

        $jobModel = new AvatarJob();
        $totalJobs = $this->db->fetch("SELECT COUNT(*) as count FROM avatar_jobs")['count'];

        $avatarModel = new Avatar();
        $totalAvatars = $this->db->fetch("SELECT COUNT(*) as count FROM avatars")['count'];

        return $this->view('admin/index', [
            'title' => 'Platform Admin Dashboard',
            'tenants' => $tenants,
            'totalUsers' => $totalUsers,
            'totalJobs' => $totalJobs,
            'totalAvatars' => $totalAvatars,
        ]);
    }

    public function tenants() {
        $tenantModel = new Tenant();
        $tenants = $tenantModel->all();

        return $this->view('admin/tenants', [
            'title' => 'Manage Tenants',
            'tenants' => $tenants,
        ]);
    }

    public function styles() {
        $styleModel = new AvatarStyle();
        $styles = $styleModel->getGlobalStyles();

        return $this->view('admin/styles', [
            'title' => 'Manage Global Styles',
            'styles' => $styles,
        ]);
    }

    public function createStyle() {
        CSRF::validate();

        $name = Request::post('name');
        $description = Request::post('description');
        $creditsCost = Request::post('credits_cost', 10);

        $styleModel = new AvatarStyle();
        $styleModel->createStyle([
            'tenant_id' => null,
            'name' => $name,
            'description' => $description,
            'credits_cost' => $creditsCost,
            'difficulty_level' => 'basic',
        ]);

        Session::flash('success', 'Global style created successfully');
        Response::redirect('/admin/styles');
    }

    public function disableTenant($tenantId) {
        CSRF::validate();

        $tenantModel = new Tenant();
        $tenantModel->update($tenantId, ['status' => 'disabled']);

        Session::flash('success', 'Tenant disabled successfully');
        Response::redirect('/admin/tenants');
    }

    public function enableTenant($tenantId) {
        CSRF::validate();

        $tenantModel = new Tenant();
        $tenantModel->update($tenantId, ['status' => 'active']);

        Session::flash('success', 'Tenant enabled successfully');
        Response::redirect('/admin/tenants');
    }
}
