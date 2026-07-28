<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Creates admin-managed homepage hero slider records.
 */
class CreateHomepageHeroSlidersTable extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `homepage_hero_sliders` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `background_image` VARCHAR(255) NOT NULL,
                `heading` VARCHAR(255) NOT NULL,
                `caption` TEXT NULL,
                `primary_cta_label` VARCHAR(120) NULL,
                `primary_cta_url` VARCHAR(255) NULL,
                `secondary_cta_label` VARCHAR(120) NULL,
                `secondary_cta_url` VARCHAR(255) NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_homepage_hero_sliders_active_order` (`is_active`, `sort_order`),
                KEY `key_homepage_hero_sliders_sort_order` (`sort_order`),
                CONSTRAINT `homepage_hero_sliders_created_by_fk`
                    FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('homepage_hero_sliders');
    }
}
