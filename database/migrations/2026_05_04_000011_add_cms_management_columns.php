<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Add admin-managed CMS fields required by the Phase 5 content module.
 */
class AddCmsManagementColumns extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            ALTER TABLE `services`
                ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) NULL AFTER `icon`,
                ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) NULL AFTER `sort_order`,
                ADD COLUMN IF NOT EXISTS `meta_description` TEXT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` VARCHAR(255) NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) NULL AFTER `canonical_url`;
        ");

        $this->connection->execute("
            ALTER TABLE `projects`
                ADD COLUMN IF NOT EXISTS `client_name` VARCHAR(150) NULL AFTER `category`,
                ADD COLUMN IF NOT EXISTS `project_date` DATE NULL AFTER `client_name`,
                ADD COLUMN IF NOT EXISTS `sort_order` INT NOT NULL DEFAULT 0 AFTER `project_date`,
                ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) NULL AFTER `sort_order`,
                ADD COLUMN IF NOT EXISTS `meta_description` TEXT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` VARCHAR(255) NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) NULL AFTER `canonical_url`;
        ");
    }

    public function down(): void
    {
        $this->connection->execute("
            ALTER TABLE `services`
                DROP COLUMN IF EXISTS `category`,
                DROP COLUMN IF EXISTS `meta_title`,
                DROP COLUMN IF EXISTS `meta_description`,
                DROP COLUMN IF EXISTS `meta_keywords`,
                DROP COLUMN IF EXISTS `canonical_url`,
                DROP COLUMN IF EXISTS `og_image`;
        ");

        $this->connection->execute("
            ALTER TABLE `projects`
                DROP COLUMN IF EXISTS `client_name`,
                DROP COLUMN IF EXISTS `project_date`,
                DROP COLUMN IF EXISTS `sort_order`,
                DROP COLUMN IF EXISTS `meta_title`,
                DROP COLUMN IF EXISTS `meta_description`,
                DROP COLUMN IF EXISTS `meta_keywords`,
                DROP COLUMN IF EXISTS `canonical_url`,
                DROP COLUMN IF EXISTS `og_image`;
        ");
    }
}
