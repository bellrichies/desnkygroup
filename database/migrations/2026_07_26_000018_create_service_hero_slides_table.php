<?php

namespace Database\Migrations;

use App\Database\Migration;

class CreateServiceHeroSlidesTable extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `service_hero_slides` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `service_id` BIGINT UNSIGNED NOT NULL,
                `heading` VARCHAR(255) NOT NULL,
                `subheading` VARCHAR(500) NULL,
                `description` TEXT NULL,
                `media_type` ENUM('image', 'video') NOT NULL DEFAULT 'image',
                `background_media` VARCHAR(255) NULL,
                `primary_cta_label` VARCHAR(120) NULL,
                `primary_cta_url` VARCHAR(255) NULL,
                `secondary_cta_label` VARCHAR(120) NULL,
                `secondary_cta_url` VARCHAR(255) NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_service_hero_active_order` (`service_id`, `is_visible`, `is_active`, `sort_order`),
                CONSTRAINT `service_hero_slides_service_fk`
                    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
                CONSTRAINT `service_hero_slides_created_by_fk`
                    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->connection->execute("
            INSERT INTO service_hero_slides
                (service_id, heading, subheading, description, media_type, background_media,
                 primary_cta_label, primary_cta_url, secondary_cta_label, secondary_cta_url,
                 sort_order, is_visible, is_active)
            SELECT id, title, summary, NULL, 'image', featured_image,
                   'Discuss your requirement', CONCAT('/contact?service=', slug, '#contact-form'),
                   'Explore capabilities', '#service-content', 0, 1, 1
            FROM services
            WHERE deleted_at IS NULL
              AND NOT EXISTS (
                  SELECT 1 FROM service_hero_slides existing WHERE existing.service_id = services.id
              )
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('service_hero_slides');
    }
}
