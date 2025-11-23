<?php
// FILE: /app/core/Auth.php

class Auth {
    public static function attempt($email, $password) {
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1",
            [$email]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            self::login($user);

            // Update last login
            $db->execute(
                "UPDATE users SET last_login_at = NOW() WHERE id = ?",
                [$user['id']]
            );

            return true;
        }

        return false;
    }

    public static function login($user) {
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('tenant_id', $user['tenant_id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
    }

    public static function logout() {
        Session::destroy();
    }

    public static function check() {
        return Session::has('user_id');
    }

    public static function guest() {
        return !self::check();
    }

    public static function user() {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => Session::get('user_id'),
            'tenant_id' => Session::get('tenant_id'),
            'role' => Session::get('user_role'),
            'name' => Session::get('user_name'),
            'email' => Session::get('user_email'),
        ];
    }

    public static function id() {
        return Session::get('user_id');
    }

    public static function tenantId() {
        return Session::get('tenant_id');
    }

    public static function role() {
        return Session::get('user_role');
    }

    public static function isPlatformAdmin() {
        return self::role() === 'platform_admin';
    }

    public static function isTenantAdmin() {
        return self::role() === 'tenant_admin';
    }

    public static function isDesigner() {
        return self::role() === 'designer';
    }

    public static function isEndUser() {
        return self::role() === 'end_user';
    }

    public static function hasRole($role) {
        return self::role() === $role;
    }

    public static function hasAnyRole($roles) {
        return in_array(self::role(), $roles);
    }

    public static function can($permission) {
        $role = self::role();

        $permissions = [
            'platform_admin' => ['manage_tenants', 'manage_global_styles', 'view_all_usage', 'manage_plans'],
            'tenant_admin' => ['manage_users', 'manage_branding', 'manage_billing', 'view_usage', 'manage_api_keys'],
            'designer' => ['manage_styles', 'manage_jobs', 'review_jobs'],
            'end_user' => ['upload_faces', 'create_jobs', 'download_avatars'],
        ];

        return isset($permissions[$role]) && in_array($permission, $permissions[$role]);
    }
}
