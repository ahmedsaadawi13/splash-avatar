<?php
// FILE: /app/controllers/HomeController.php

class HomeController extends Controller {
    public function index() {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        return $this->view('home/index', [
            'title' => 'SplashAvatar - AI Avatar Generator SaaS',
        ]);
    }
}
