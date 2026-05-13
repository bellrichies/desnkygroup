<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Create communication and reset-token tables.
 */
class CreateCommunicationTables extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `name` VARCHAR(150) NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'subscribed',
                `source` VARCHAR(100) NULL,
                `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `admin_user_id` BIGINT UNSIGNED NOT NULL,
                `token_hash` VARCHAR(255) NOT NULL,
                `expires_at` TIMESTAMP NOT NULL,
                `used_at` TIMESTAMP NULL DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT `password_reset_tokens_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('password_reset_tokens');
        $this->dropIfExists('newsletter_subscribers');
    }
}
