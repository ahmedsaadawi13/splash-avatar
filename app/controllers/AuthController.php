<?php
// FILE: /app/controllers/AuthController.php

class AuthController extends Controller {
    public function showLogin() {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        return $this->view('auth/login', [
            'title' => 'Login - SplashAvatar',
        ], 'auth');
    }

    public function login() {
        CSRF::validate();

        $email = Request::post('email');
        $password = Request::post('password');

        if (!ValidationHelper::email($email)) {
            Session::flash('error', 'Please provide a valid email address');
            Response::redirect('/login');
        }

        if (Auth::attempt($email, $password)) {
            Session::flash('success', 'Welcome back!');
            Response::redirect('/dashboard');
        } else {
            Session::flash('error', 'Invalid credentials');
            Response::redirect('/login');
        }
    }

    public function showRegister() {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }

        $planModel = new Plan();
        $plans = $planModel->getAllActive();

        return $this->view('auth/register', [
            'title' => 'Register - SplashAvatar',
            'plans' => $plans,
        ], 'auth');
    }

    public function register() {
        CSRF::validate();

        $name = Request::post('name');
        $email = Request::post('email');
        $password = Request::post('password');
        $companyName = Request::post('company_name');
        $planId = Request::post('plan_id', 1);

        if (!ValidationHelper::required($name) || !ValidationHelper::required($email) || !ValidationHelper::required($password)) {
            Session::flash('error', 'All fields are required');
            Response::redirect('/register');
        }

        if (!ValidationHelper::email($email)) {
            Session::flash('error', 'Please provide a valid email address');
            Response::redirect('/register');
        }

        if (!ValidationHelper::minLength($password, 6)) {
            Session::flash('error', 'Password must be at least 6 characters');
            Response::redirect('/register');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            Session::flash('error', 'Email already exists');
            Response::redirect('/register');
        }

        try {
            $this->db->beginTransaction();

            $tenantModel = new Tenant();
            $tenantId = $tenantModel->createTenant([
                'name' => $companyName ?: $name . "'s Company",
                'status' => 'active',
            ]);

            $userId = $userModel->createUser([
                'tenant_id' => $tenantId,
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'tenant_admin',
                'status' => 'active',
            ]);

            $subscriptionModel = new TenantSubscription();
            $subscriptionModel->createSubscription($tenantId, $planId, 'trialing');

            $this->db->commit();

            $user = $userModel->find($userId);
            Auth::login($user);

            Session::flash('success', 'Account created successfully! Welcome to SplashAvatar.');
            Response::redirect('/dashboard');

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Registration error: ' . $e->getMessage());
            Session::flash('error', 'Registration failed. Please try again.');
            Response::redirect('/register');
        }
    }

    public function logout() {
        Auth::logout();
        Session::flash('success', 'You have been logged out');
        Response::redirect('/login');
    }
}
