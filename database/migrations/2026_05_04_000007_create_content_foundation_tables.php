<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Create content, SEO, media, service, and project tables.
 */
class CreateContentFoundationTables extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `page_sections` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `page_id` BIGINT UNSIGNED NOT NULL,
                `section_key` VARCHAR(100) NOT NULL,
                `heading` VARCHAR(255) NULL,
                `body` TEXT NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT `page_sections_page_id_fk`
                    FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `services` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `summary` TEXT NULL,
                `content` TEXT NULL,
                `icon` VARCHAR(100) NULL,
                `featured_image` VARCHAR(255) NULL,
                `is_published` TINYINT(1) NOT NULL DEFAULT 0,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                KEY `key_services_is_published` (`is_published`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `projects` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `summary` TEXT NULL,
                `description` TEXT NULL,
                `category` VARCHAR(100) NULL,
                `featured_image` VARCHAR(255) NULL,
                `is_published` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `project_images` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `project_id` BIGINT UNSIGNED NOT NULL,
                `path` VARCHAR(255) NOT NULL,
                `alt_text` VARCHAR(255) NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT `project_images_project_id_fk`
                    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `media_library` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `disk` VARCHAR(50) NOT NULL DEFAULT 'public',
                `path` VARCHAR(255) NOT NULL,
                `filename` VARCHAR(255) NOT NULL,
                `mime_type` VARCHAR(100) NOT NULL,
                `size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `alt_text` VARCHAR(255) NULL,
                `title` VARCHAR(255) NULL,
                `uploaded_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT `media_library_uploaded_by_fk`
                    FOREIGN KEY (`uploaded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `seo_metadata` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `resource_type` VARCHAR(100) NOT NULL,
                `resource_id` BIGINT UNSIGNED NOT NULL,
                `meta_title` VARCHAR(255) NULL,
                `meta_description` TEXT NULL,
                `meta_keywords` VARCHAR(255) NULL,
                `canonical_url` VARCHAR(255) NULL,
                `og_image` VARCHAR(255) NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `unique_seo_resource` (`resource_type`, `resource_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `site_settings` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `setting_key` VARCHAR(150) NOT NULL UNIQUE,
                `setting_value` TEXT NULL,
                `setting_type` VARCHAR(50) NOT NULL DEFAULT 'string',
                `is_public` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('site_settings');
        $this->dropIfExists('seo_metadata');
        $this->dropIfExists('media_library');
        $this->dropIfExists('project_images');
        $this->dropIfExists('projects');
        $this->dropIfExists('services');
        $this->dropIfExists('page_sections');
    }
}
