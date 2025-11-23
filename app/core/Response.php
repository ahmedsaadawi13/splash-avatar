<?php
// FILE: /app/core/Response.php

class Response {
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function redirect($url, $statusCode = 302) {
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }

    public static function back() {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        self::redirect($referer);
    }

    public static function setStatusCode($code) {
        http_response_code($code);
    }

    public static function setHeader($key, $value) {
        header("{$key}: {$value}");
    }

    public static function download($filePath, $fileName = null) {
        if (!file_exists($filePath)) {
            self::setStatusCode(404);
            echo '404 Not Found';
            exit;
        }

        if ($fileName === null) {
            $fileName = basename($filePath);
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public static function notFound() {
        self::setStatusCode(404);
        echo '404 Not Found';
        exit;
    }

    public static function forbidden() {
        self::setStatusCode(403);
        echo '403 Forbidden';
        exit;
    }

    public static function unauthorized() {
        self::setStatusCode(401);
        echo '401 Unauthorized';
        exit;
    }
}
