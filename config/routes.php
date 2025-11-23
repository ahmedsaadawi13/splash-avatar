<?php
// FILE: /config/routes.php

// Public routes
$router->get('/', 'HomeController@index');

// Auth routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Dashboard (requires auth)
$router->get('/dashboard', 'DashboardController@index', ['AuthMiddleware', 'TenantMiddleware']);

// Face uploads
$router->get('/faces', 'FaceUploadController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/faces/upload', 'FaceUploadController@showUpload', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/faces/upload', 'FaceUploadController@upload', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/faces/delete/{id}', 'FaceUploadController@delete', ['AuthMiddleware', 'TenantMiddleware']);

// Styles
$router->get('/styles', 'StyleController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/styles/{slug}', 'StyleController@view', ['AuthMiddleware', 'TenantMiddleware']);

// Avatar jobs
$router->get('/jobs', 'AvatarJobController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/jobs/create', 'AvatarJobController@showCreate', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/jobs/create', 'AvatarJobController@create', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/jobs/run/{id}', 'AvatarJobController@run', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/jobs/view/{id}', 'AvatarJobController@view', ['AuthMiddleware', 'TenantMiddleware']);

// Avatar packs
$router->get('/packs', 'PackController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/packs/{id}', 'PackController@view', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/packs/download/{id}', 'PackController@download', ['AuthMiddleware', 'TenantMiddleware']);
$router->get('/packs/download-zip/{id}', 'PackController@downloadPack', ['AuthMiddleware', 'TenantMiddleware']);

// Branding
$router->get('/branding', 'BrandingController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/branding/update', 'BrandingController@update', ['AuthMiddleware', 'TenantMiddleware']);

// Billing
$router->get('/billing', 'BillingController@index', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/billing/generate-api-key', 'BillingController@generateApiKey', ['AuthMiddleware', 'TenantMiddleware']);
$router->post('/billing/revoke-key/{id}', 'BillingController@revokeApiKey', ['AuthMiddleware', 'TenantMiddleware']);

// Admin routes
$router->get('/admin', 'AdminController@index', ['AuthMiddleware']);
$router->get('/admin/tenants', 'AdminController@tenants', ['AuthMiddleware']);
$router->get('/admin/styles', 'AdminController@styles', ['AuthMiddleware']);
$router->post('/admin/create-style', 'AdminController@createStyle', ['AuthMiddleware']);
$router->post('/admin/disable-tenant/{id}', 'AdminController@disableTenant', ['AuthMiddleware']);
$router->post('/admin/enable-tenant/{id}', 'AdminController@enableTenant', ['AuthMiddleware']);

// API routes
$router->post('/api/v1/avatar-jobs/create', 'ApiController@createJob');
$router->get('/api/v1/avatar-jobs/{id}', 'ApiController@getJobStatus');
$router->get('/api/v1/avatar-jobs/{id}/avatars', 'ApiController@getJobAvatars');
$router->get('/api/v1/avatar-packs/{id}', 'ApiController@getPack');
