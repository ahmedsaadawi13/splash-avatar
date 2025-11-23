<?php
// FILE: /app/middleware/AuthMiddleware.php

class AuthMiddleware {
    public function handle() {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to continue');
            Response::redirect('/login');
        }
    }
}
