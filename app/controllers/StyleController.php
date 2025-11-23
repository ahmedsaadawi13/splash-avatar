<?php
// FILE: /app/controllers/StyleController.php

class StyleController extends Controller {
    public function index() {
        $tenantId = Auth::tenantId();

        $styleModel = new AvatarStyle();
        $styles = $styleModel->getAll($tenantId);

        return $this->view('styles/index', [
            'title' => 'Avatar Styles',
            'styles' => $styles,
        ]);
    }

    public function view($slug) {
        $tenantId = Auth::tenantId();

        $styleModel = new AvatarStyle();
        $style = $styleModel->getBySlug($slug, $tenantId);

        if (!$style) {
            Session::flash('error', 'Style not found');
            Response::redirect('/styles');
        }

        return $this->view('styles/view', [
            'title' => $style['name'],
            'style' => $style,
        ]);
    }
}
