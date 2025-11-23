<?php
// FILE: /app/helpers/ValidationHelper.php

class ValidationHelper {
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value) {
        return !empty(trim($value));
    }

    public static function minLength($value, $min) {
        return strlen($value) >= $min;
    }

    public static function maxLength($value, $max) {
        return strlen($value) <= $max;
    }

    public static function numeric($value) {
        return is_numeric($value);
    }

    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function inArray($value, $array) {
        return in_array($value, $array);
    }

    public static function validRole($role) {
        $validRoles = ['platform_admin', 'tenant_admin', 'designer', 'end_user'];
        return self::inArray($role, $validRoles);
    }

    public static function validStatus($status) {
        $validStatuses = ['active', 'disabled', 'pending', 'trialing', 'past_due', 'canceled'];
        return self::inArray($status, $validStatuses);
    }

    public static function validJobStatus($status) {
        $validStatuses = ['pending', 'processing', 'completed', 'failed'];
        return self::inArray($status, $validStatuses);
    }

    public static function validUploadStatus($status) {
        $validStatuses = ['uploaded', 'validated', 'rejected'];
        return self::inArray($status, $validStatuses);
    }

    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public static function positiveNumber($value) {
        return is_numeric($value) && $value > 0;
    }

    public static function range($value, $min, $max) {
        return is_numeric($value) && $value >= $min && $value <= $max;
    }

    public static function alphanumeric($value) {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    public static function slug($value) {
        return preg_match('/^[a-z0-9-]+$/', $value);
    }

    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitize($value);
            }
        }
        return $sanitized;
    }
}
