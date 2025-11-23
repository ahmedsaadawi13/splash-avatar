<?php
// FILE: /app/core/Controller.php

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view($viewPath, $data = []) {
        $view = new View();
        return $view->render($viewPath, $data);
    }

    protected function json($data, $statusCode = 200) {
        Response::json($data, $statusCode);
    }

    protected function redirect($url) {
        Response::redirect($url);
    }

    protected function back() {
        Response::back();
    }

    protected function validate($rules) {
        foreach ($rules as $field => $rule) {
            $value = Request::input($field);
            $ruleParts = explode('|', $rule);

            foreach ($ruleParts as $rulePart) {
                if ($rulePart === 'required' && empty($value)) {
                    Session::flash('error', ucfirst(str_replace('_', ' ', $field)) . ' is required');
                    return false;
                }

                if (strpos($rulePart, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    Session::flash('error', ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email');
                    return false;
                }

                if (strpos($rulePart, 'min:') !== false) {
                    $min = (int) str_replace('min:', '', $rulePart);
                    if (strlen($value) < $min) {
                        Session::flash('error', ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters");
                        return false;
                    }
                }

                if (strpos($rulePart, 'max:') !== false) {
                    $max = (int) str_replace('max:', '', $rulePart);
                    if (strlen($value) > $max) {
                        Session::flash('error', ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters");
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
