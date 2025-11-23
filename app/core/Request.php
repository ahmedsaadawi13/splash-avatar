<?php
// FILE: /app/core/Request.php

class Request {
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost() {
        return self::method() === 'POST';
    }

    public static function isGet() {
        return self::method() === 'GET';
    }

    public static function isPut() {
        return self::method() === 'PUT';
    }

    public static function isDelete() {
        return self::method() === 'DELETE';
    }

    public static function uri() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        return $uri;
    }

    public static function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? self::clean($_GET[$key]) : $default;
    }

    public static function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? self::clean($_POST[$key]) : $default;
    }

    public static function input($key = null, $default = null) {
        if (self::isPost()) {
            return self::post($key, $default);
        }
        return self::get($key, $default);
    }

    public static function all() {
        return array_merge($_GET, $_POST);
    }

    public static function has($key) {
        return isset($_GET[$key]) || isset($_POST[$key]);
    }

    public static function file($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function hasFile($key) {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public static function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function userAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public static function header($key) {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public static function getJson() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }
        return trim($data);
    }
}
