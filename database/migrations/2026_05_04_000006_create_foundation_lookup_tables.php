<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Create foundation lookup and access-control tables.
 */
class CreateFoundationLookupTables extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `roles` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL UNIQUE,
                `slug` VARCHAR(100) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `is_system_role` TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `permissions` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(150) NOT NULL,
                `slug` VARCHAR(150) NOT NULL UNIQUE,
                `module` VARCHAR(100) NOT NULL,
                `description` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `admin_user_roles` (
                `admin_user_id` BIGINT UNSIGNED NOT NULL,
                `role_id` BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (`admin_user_id`, `role_id`),
                CONSTRAINT `admin_user_roles_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `admin_user_roles_role_id_fk`
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `role_permissions` (
                `role_id` BIGINT UNSIGNED NOT NULL,
                `permission_id` BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (`role_id`, `permission_id`),
                CONSTRAINT `role_permissions_role_id_fk`
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
                CONSTRAINT `role_permissions_permission_id_fk`
                    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `admin_user_id` BIGINT UNSIGNED NULL,
                `action` VARCHAR(150) NOT NULL,
                `module` VARCHAR(100) NOT NULL,
                `description` TEXT NULL,
                `ip_address` VARCHAR(45) NULL,
                `user_agent` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_admin_activity_logs_module` (`module`),
                CONSTRAINT `admin_activity_logs_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('admin_activity_logs');
        $this->dropIfExists('role_permissions');
        $this->dropIfExists('admin_user_roles');
        $this->dropIfExists('permissions');
        $this->dropIfExists('roles');
    }
}
