<?php
// FILE: /app/controllers/BrandingController.php

class BrandingController extends Controller {
    public function index() {
        if (!Auth::hasAnyRole(['tenant_admin', 'platform_admin'])) {
            Session::flash('error', 'You do not have permission to access branding settings');
            Response::redirect('/dashboard');
        }

        $tenantId = Auth::tenantId();

        $brandingModel = new TenantBranding();
        $branding = $brandingModel->getByTenant($tenantId);

        return $this->view('branding/index', [
            'title' => 'Branding Settings',
            'branding' => $branding,
        ]);
    }

    public function update() {
        if (!Auth::hasAnyRole(['tenant_admin', 'platform_admin'])) {
            Session::flash('error', 'You do not have permission to update branding settings');
            Response::redirect('/dashboard');
        }

        CSRF::validate();

        $tenantId = Auth::tenantId();

        $brandName = Request::post('brand_name');
        $primaryColor = Request::post('primary_color', '#007bff');
        $secondaryColor = Request::post('secondary_color', '#6c757d');
        $watermarkText = Request::post('watermark_text', '');
        $watermarkOpacity = Request::post('watermark_opacity', 50);

        $data = [
            'brand_name' => $brandName,
            'primary_color' => $primaryColor,
            'secondary_color' => $secondaryColor,
            'watermark_text' => $watermarkText,
            'watermark_opacity' => $watermarkOpacity,
        ];

        if (Request::hasFile('logo')) {
            try {
                $fileUploader = new FileUploadHelper();
                $uploadedFile = $fileUploader->upload($_FILES['logo'], 'branding/' . $tenantId);
                $data['logo_path'] = $uploadedFile['file_path'];
            } catch (Exception $e) {
                Session::flash('error', 'Logo upload failed: ' . $e->getMessage());
                Response::redirect('/branding');
            }
        }

        $brandingModel = new TenantBranding();
        $brandingModel->upsert($tenantId, $data);

        Session::flash('success', 'Branding settings updated successfully');
        Response::redirect('/branding');
    }
}
