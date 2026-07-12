<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Adds trusted_clients and faqs tables; extends newsletter_subscribers for admin management.
 */
class AddCmsEngagementTables extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `trusted_clients` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `logo` VARCHAR(255) NULL,
                `website_url` VARCHAR(255) NULL,
                `service_slug` VARCHAR(255) NULL COMMENT 'Links to services.slug; NULL = show on all services',
                `sort_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_trusted_clients_is_active` (`is_active`),
                KEY `key_trusted_clients_service_slug` (`service_slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `faqs` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `question` VARCHAR(500) NOT NULL,
                `answer` TEXT NOT NULL,
                `category` VARCHAR(100) NULL DEFAULT 'General',
                `sort_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_faqs_is_active` (`is_active`),
                KEY `key_faqs_category` (`category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Extend newsletter_subscribers with unsubscribe support and admin notes if not present.
        // Using IF NOT EXISTS column guard via ALTER IGNORE for idempotency.
        $cols = $this->connection->query(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'newsletter_subscribers'"
        );
        $existing = array_column($cols, 'COLUMN_NAME');

        if (!in_array('status', $existing, true)) {
            $this->connection->execute(
                "ALTER TABLE `newsletter_subscribers` ADD COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'subscribed' AFTER `source`"
            );
        }
        if (!in_array('unsubscribed_at', $existing, true)) {
            $this->connection->execute(
                "ALTER TABLE `newsletter_subscribers` ADD COLUMN `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL AFTER `status`"
            );
        }
        if (!in_array('updated_at', $existing, true)) {
            $this->connection->execute(
                "ALTER TABLE `newsletter_subscribers` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`"
            );
        }

        // Extend contacts table with archive status if not already present.
        $contactCols = $this->connection->query(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contacts'"
        );
        $existingContact = array_column($contactCols, 'COLUMN_NAME');

        if (!in_array('archived_at', $existingContact, true)) {
            $this->connection->execute(
                "ALTER TABLE `contacts` ADD COLUMN `archived_at` TIMESTAMP NULL DEFAULT NULL AFTER `responded_at`"
            );
        }
    }

    public function down(): void
    {
        $this->dropIfExists('faqs');
        $this->dropIfExists('trusted_clients');
    }
}
