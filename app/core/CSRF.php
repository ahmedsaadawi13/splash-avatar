<?php
// FILE: /app/core/CSRF.php

class CSRF {
    public static function generateToken() {
        Session::start();
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }

    public static function validateToken($token) {
        Session::start();
        $sessionToken = Session::get('csrf_token');
        return $sessionToken && hash_equals($sessionToken, $token);
    }

    public static function field() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    public static function validate() {
        $token = Request::post('csrf_token');
        if (!$token || !self::validateToken($token)) {
            Response::json(['status' => 'error', 'message' => 'Invalid CSRF token'], 403);
            exit;
        }
        return true;
    }
}
