<?php
// FILE: /app/middleware/RoleMiddleware.php

class RoleMiddleware {
    private $allowedRoles;

    public function __construct($allowedRoles = []) {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    }

    public function handle() {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to continue');
            Response::redirect('/login');
        }

        if (!empty($this->allowedRoles) && !Auth::hasAnyRole($this->allowedRoles)) {
            Session::flash('error', 'You do not have permission to access this resource');
            Response::redirect('/dashboard');
        }
    }
}
