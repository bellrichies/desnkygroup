<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Expand RBAC tables for Phase 7 admin user management.
 */
class ExpandRbacPhaseSevenTables extends Migration
{
    public function up(): void
    {
        $this->addColumn('roles', 'sort_order', '`sort_order` INT NOT NULL DEFAULT 0 AFTER `is_system_role`');
        $this->addIndex('roles', 'key_roles_sort_order', '`sort_order`');

        $this->addColumn('permissions', 'parent_id', '`parent_id` BIGINT UNSIGNED NULL AFTER `module`');
        $this->addIndex('permissions', 'key_permissions_parent_id', '`parent_id`');

        $this->addColumn('admin_users', 'force_password_reset', '`force_password_reset` TINYINT(1) NOT NULL DEFAULT 0 AFTER `password_hash`');
        $this->addColumn('admin_users', 'suspended_at', '`suspended_at` TIMESTAMP NULL AFTER `is_active`');
        $this->addColumn('admin_users', 'last_login_ip', '`last_login_ip` VARCHAR(45) NULL AFTER `last_login_at`');

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `admin_sessions` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `admin_user_id` BIGINT UNSIGNED NOT NULL,
                `session_id` VARCHAR(150) NOT NULL,
                `ip_address` VARCHAR(45) NULL,
                `user_agent` TEXT NULL,
                `last_activity_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `unique_admin_sessions_session_id` (`session_id`),
                KEY `key_admin_sessions_admin_user_id` (`admin_user_id`),
                CONSTRAINT `admin_sessions_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('admin_sessions');
    }

    private function addColumn(string $table, string $column, string $definition): void
    {
        if ($this->columnExists($table, $column)) {
            return;
        }

        $this->connection->execute("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
    }

    private function addIndex(string $table, string $index, string $columns): void
    {
        if ($this->indexExists($table, $index)) {
            return;
        }

        $this->connection->execute("ALTER TABLE `{$table}` ADD INDEX `{$index}` ({$columns})");
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            [$table, $column]
        );

        return (int) ($row['aggregate'] ?? 0) > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$table, $index]
        );

        return (int) ($row['aggregate'] ?? 0) > 0;
    }
}
