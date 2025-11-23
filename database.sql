-- FILE: /database.sql
-- SplashAvatar Multi-Tenant SaaS Database Schema

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `splashavatar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `splashavatar`;

-- =====================================================
-- TENANTS TABLE
-- =====================================================
CREATE TABLE `tenants` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `status` enum('active','disabled','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('platform_admin','tenant_admin','designer','end_user') NOT NULL DEFAULT 'end_user',
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `last_login_at` datetime NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PLANS TABLE
-- =====================================================
CREATE TABLE `plans` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price_monthly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_yearly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_credits` int(11) NOT NULL DEFAULT 0,
  `max_users` int(11) DEFAULT NULL,
  `max_storage_mb` int(11) DEFAULT NULL,
  `features_json` text NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TENANT SUBSCRIPTIONS TABLE
-- =====================================================
CREATE TABLE `tenant_subscriptions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `plan_id` int(11) UNSIGNED NOT NULL,
  `status` enum('trialing','active','past_due','canceled') NOT NULL DEFAULT 'active',
  `start_date` date NOT NULL,
  `end_date` date NULL,
  `renewal_date` date NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_plan_id` (`plan_id`),
  KEY `idx_status` (`status`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TENANT CREDITS TABLE
-- =====================================================
CREATE TABLE `tenant_credits` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `month` varchar(7) NOT NULL COMMENT 'YYYY-MM format',
  `credits_allocated` int(11) NOT NULL DEFAULT 0,
  `credits_used` int(11) NOT NULL DEFAULT 0,
  `credits_remaining` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tenant_month` (`tenant_id`, `month`),
  KEY `idx_month` (`month`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TENANT USAGE TABLE
-- =====================================================
CREATE TABLE `tenant_usage` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `description` text NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TENANT BRANDING TABLE
-- =====================================================
CREATE TABLE `tenant_branding` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `brand_name` varchar(255) NULL,
  `logo_path` varchar(500) NULL,
  `primary_color` varchar(7) DEFAULT '#007bff',
  `secondary_color` varchar(7) DEFAULT '#6c757d',
  `watermark_text` varchar(255) NULL,
  `watermark_opacity` int(3) DEFAULT 50,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tenant_id` (`tenant_id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATAR STYLES TABLE
-- =====================================================
CREATE TABLE `avatar_styles` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NULL COMMENT 'NULL for global styles',
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NULL,
  `preview_image_path` varchar(500) NULL,
  `difficulty_level` enum('basic','premium') NOT NULL DEFAULT 'basic',
  `credits_cost` int(11) NOT NULL DEFAULT 10,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_is_active` (`is_active`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STYLE TAGS TABLE
-- =====================================================
CREATE TABLE `style_tags` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_slug` (`slug`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STYLE TAG LINKS TABLE
-- =====================================================
CREATE TABLE `style_tag_links` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NULL,
  `style_id` int(11) UNSIGNED NOT NULL,
  `tag_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_style_id` (`style_id`),
  KEY `idx_tag_id` (`tag_id`),
  FOREIGN KEY (`style_id`) REFERENCES `avatar_styles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `style_tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FACE UPLOADS TABLE
-- =====================================================
CREATE TABLE `face_uploads` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size_bytes` int(11) NOT NULL,
  `face_detected` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('uploaded','validated','rejected') NOT NULL DEFAULT 'uploaded',
  `rejection_reason` varchar(500) NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATAR JOBS TABLE
-- =====================================================
CREATE TABLE `avatar_jobs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NULL,
  `title` varchar(255) NOT NULL,
  `description` text NULL,
  `credits_cost` int(11) NOT NULL DEFAULT 0,
  `total_avatars_requested` int(11) NOT NULL DEFAULT 0,
  `total_avatars_generated` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATAR JOB FACES TABLE
-- =====================================================
CREATE TABLE `avatar_job_faces` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `job_id` int(11) UNSIGNED NOT NULL,
  `face_upload_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_job_id` (`job_id`),
  KEY `idx_face_upload_id` (`face_upload_id`),
  FOREIGN KEY (`job_id`) REFERENCES `avatar_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`face_upload_id`) REFERENCES `face_uploads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATAR JOB STYLES TABLE
-- =====================================================
CREATE TABLE `avatar_job_styles` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `job_id` int(11) UNSIGNED NOT NULL,
  `style_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_job_id` (`job_id`),
  KEY `idx_style_id` (`style_id`),
  FOREIGN KEY (`job_id`) REFERENCES `avatar_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`style_id`) REFERENCES `avatar_styles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATARS TABLE
-- =====================================================
CREATE TABLE `avatars` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `job_id` int(11) UNSIGNED NOT NULL,
  `style_id` int(11) UNSIGNED NOT NULL,
  `face_upload_id` int(11) UNSIGNED NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `thumbnail_path` varchar(500) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `is_watermarked` tinyint(1) NOT NULL DEFAULT 0,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_job_id` (`job_id`),
  KEY `idx_style_id` (`style_id`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`job_id`) REFERENCES `avatar_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`style_id`) REFERENCES `avatar_styles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`face_upload_id`) REFERENCES `face_uploads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- AVATAR PACKS TABLE
-- =====================================================
CREATE TABLE `avatar_packs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `job_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NULL,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `total_avatars` int(11) NOT NULL DEFAULT 0,
  `cover_image_path` varchar(500) NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_job_id` (`job_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`job_id`) REFERENCES `avatar_jobs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DOWNLOADS LOG TABLE
-- =====================================================
CREATE TABLE `downloads_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `avatar_id` int(11) UNSIGNED NULL,
  `pack_id` int(11) UNSIGNED NULL,
  `user_id` int(11) UNSIGNED NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_avatar_id` (`avatar_id`),
  KEY `idx_pack_id` (`pack_id`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TENANT API KEYS TABLE
-- =====================================================
CREATE TABLE `tenant_api_keys` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NOT NULL,
  `api_key` varchar(100) NOT NULL UNIQUE,
  `label` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `rate_limit_per_minute` int(11) NOT NULL DEFAULT 60,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_api_key` (`api_key`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_is_active` (`is_active`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTIVITY LOGS TABLE
-- =====================================================
CREATE TABLE `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) UNSIGNED NULL,
  `user_id` int(11) UNSIGNED NULL,
  `action` varchar(100) NOT NULL,
  `description` text NULL,
  `metadata` text NULL COMMENT 'JSON data',
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SEED DATA
-- =====================================================

-- Insert demo plans
INSERT INTO `plans` (`id`, `name`, `price_monthly`, `price_yearly`, `monthly_credits`, `max_users`, `max_storage_mb`, `features_json`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Free', 0.00, 0.00, 100, 1, 500, '{"hires_output":false,"api_access":false,"custom_styles":false}', 1, NOW(), NOW()),
(2, 'Starter', 29.00, 290.00, 500, 5, 2000, '{"hires_output":true,"api_access":false,"custom_styles":false}', 1, NOW(), NOW()),
(3, 'Pro', 99.00, 990.00, 2000, 20, 10000, '{"hires_output":true,"api_access":true,"custom_styles":true}', 1, NOW(), NOW()),
(4, 'Enterprise', 299.00, 2990.00, 10000, NULL, NULL, '{"hires_output":true,"api_access":true,"custom_styles":true,"priority_support":true}', 1, NOW(), NOW());

-- Insert demo tenant
INSERT INTO `tenants` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Demo Company', 'demo-company', 'active', NOW(), NOW());

-- Insert platform admin user (password: admin123)
INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Platform Admin', 'admin@splashavatar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'platform_admin', 'active', NOW(), NOW());

-- Insert demo tenant admin (password: demo123)
INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 'Demo Admin', 'demo@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tenant_admin', 'active', NOW(), NOW());

-- Insert demo end user (password: user123)
INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 'Demo User', 'user@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'end_user', 'active', NOW(), NOW());

-- Insert active subscription for demo tenant
INSERT INTO `tenant_subscriptions` (`id`, `tenant_id`, `plan_id`, `status`, `start_date`, `end_date`, `renewal_date`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'active', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), DATE_ADD(CURDATE(), INTERVAL 1 MONTH), NOW(), NOW());

-- Insert credit balance for demo tenant
INSERT INTO `tenant_credits` (`id`, `tenant_id`, `month`, `credits_allocated`, `credits_used`, `credits_remaining`, `created_at`, `updated_at`) VALUES
(1, 1, DATE_FORMAT(NOW(), '%Y-%m'), 500, 50, 450, NOW(), NOW());

-- Insert global avatar styles
INSERT INTO `avatar_styles` (`id`, `tenant_id`, `name`, `slug`, `description`, `difficulty_level`, `credits_cost`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Anime Neon', 'anime-neon', 'Vibrant anime style with neon colors', 'basic', 10, 1, NOW(), NOW()),
(2, NULL, '3D Pixar-like', '3d-pixar-like', 'Professional 3D rendering in Pixar animation style', 'premium', 15, 1, NOW(), NOW()),
(3, NULL, 'Cartoon Classic', 'cartoon-classic', 'Classic cartoon style with bold outlines', 'basic', 10, 1, NOW(), NOW()),
(4, NULL, 'Pixel Retro', 'pixel-retro', '8-bit pixel art style', 'basic', 8, 1, NOW(), NOW()),
(5, NULL, 'Cyberpunk Neon', 'cyberpunk-neon', 'Futuristic cyberpunk aesthetic with neon highlights', 'premium', 12, 1, NOW(), NOW()),
(6, NULL, 'Watercolor Art', 'watercolor-art', 'Soft watercolor painting style', 'basic', 10, 1, NOW(), NOW());

-- Insert sample face upload
INSERT INTO `face_uploads` (`id`, `tenant_id`, `user_id`, `title`, `file_path`, `original_file_name`, `mime_type`, `size_bytes`, `face_detected`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Sample Face Photo', 'faces/1/sample_face.jpg', 'photo.jpg', 'image/jpeg', 245000, 1, 'validated', NOW(), NOW());

-- Insert sample avatar job
INSERT INTO `avatar_jobs` (`id`, `tenant_id`, `user_id`, `title`, `description`, `credits_cost`, `total_avatars_requested`, `total_avatars_generated`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Sample Avatar Job', 'Generated sample avatars for demo', 50, 6, 6, 'completed', NOW(), NOW());

-- Link face to job
INSERT INTO `avatar_job_faces` (`tenant_id`, `job_id`, `face_upload_id`, `created_at`) VALUES
(1, 1, 1, NOW());

-- Link styles to job
INSERT INTO `avatar_job_styles` (`tenant_id`, `job_id`, `style_id`, `created_at`) VALUES
(1, 1, 1, NOW()),
(1, 1, 2, NOW()),
(1, 1, 3, NOW()),
(1, 1, 4, NOW()),
(1, 1, 5, NOW()),
(1, 1, 6, NOW());

-- Insert sample generated avatars
INSERT INTO `avatars` (`tenant_id`, `job_id`, `style_id`, `face_upload_id`, `image_path`, `thumbnail_path`, `width`, `height`, `is_watermarked`, `download_count`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'avatars/1/avatar_1_anime.jpg', 'avatars/1/avatar_1_anime_thumb.jpg', 512, 512, 0, 0, NOW(), NOW()),
(1, 1, 2, 1, 'avatars/1/avatar_1_3d.jpg', 'avatars/1/avatar_1_3d_thumb.jpg', 512, 512, 0, 0, NOW(), NOW()),
(1, 1, 3, 1, 'avatars/1/avatar_1_cartoon.jpg', 'avatars/1/avatar_1_cartoon_thumb.jpg', 512, 512, 0, 0, NOW(), NOW()),
(1, 1, 4, 1, 'avatars/1/avatar_1_pixel.jpg', 'avatars/1/avatar_1_pixel_thumb.jpg', 512, 512, 0, 0, NOW(), NOW()),
(1, 1, 5, 1, 'avatars/1/avatar_1_cyber.jpg', 'avatars/1/avatar_1_cyber_thumb.jpg', 512, 512, 0, 0, NOW(), NOW()),
(1, 1, 6, 1, 'avatars/1/avatar_1_watercolor.jpg', 'avatars/1/avatar_1_watercolor_thumb.jpg', 512, 512, 0, 0, NOW(), NOW());

-- Insert sample avatar pack
INSERT INTO `avatar_packs` (`id`, `tenant_id`, `job_id`, `user_id`, `name`, `description`, `total_avatars`, `cover_image_path`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'Sample Avatar Job - Avatar Pack', 'Generated sample avatars for demo', 6, 'avatars/1/avatar_1_anime_thumb.jpg', NOW(), NOW());

-- Insert sample API key for demo tenant
INSERT INTO `tenant_api_keys` (`tenant_id`, `api_key`, `label`, `is_active`, `rate_limit_per_minute`, `created_at`, `updated_at`) VALUES
(1, 'sk_demo_1234567890abcdefghijklmnopqrstuvwxyz1234567890abcdef', 'Demo API Key', 1, 60, NOW(), NOW());

-- Insert sample branding for demo tenant
INSERT INTO `tenant_branding` (`tenant_id`, `brand_name`, `primary_color`, `secondary_color`, `watermark_text`, `watermark_opacity`, `created_at`, `updated_at`) VALUES
(1, 'Demo Company', '#007bff', '#6c757d', 'Demo Company', 50, NOW(), NOW());
