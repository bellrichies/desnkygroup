<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Create the production blogging module schema.
 */
class CreateBloggingSystemTables extends Migration
{
    public function up(): void
    {
        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_authors` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `admin_user_id` BIGINT UNSIGNED NULL,
                `display_name` VARCHAR(160) NOT NULL,
                `slug` VARCHAR(180) NOT NULL UNIQUE,
                `title` VARCHAR(160) NULL,
                `bio` TEXT NULL,
                `avatar` VARCHAR(255) NULL,
                `email` VARCHAR(190) NULL,
                `linkedin_url` VARCHAR(255) NULL,
                `x_url` VARCHAR(255) NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_blog_authors_admin_user_id` (`admin_user_id`),
                KEY `key_blog_authors_is_active` (`is_active`),
                CONSTRAINT `blog_authors_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_categories` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(160) NOT NULL,
                `slug` VARCHAR(180) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `seo_title` VARCHAR(255) NULL,
                `meta_description` TEXT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                KEY `key_blog_categories_active_sort` (`is_active`, `sort_order`),
                KEY `key_blog_categories_deleted_at` (`deleted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_tags` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(120) NOT NULL,
                `slug` VARCHAR(140) NOT NULL UNIQUE,
                `description` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                KEY `key_blog_tags_deleted_at` (`deleted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_posts` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `author_id` BIGINT UNSIGNED NULL,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `excerpt` TEXT NULL,
                `content` MEDIUMTEXT NOT NULL,
                `featured_image` VARCHAR(255) NULL,
                `featured_image_alt` VARCHAR(255) NULL,
                `status` VARCHAR(30) NOT NULL DEFAULT 'draft',
                `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
                `published_at` TIMESTAMP NULL DEFAULT NULL,
                `scheduled_at` TIMESTAMP NULL DEFAULT NULL,
                `seo_title` VARCHAR(255) NULL,
                `meta_description` TEXT NULL,
                `canonical_url` VARCHAR(255) NULL,
                `og_image` VARCHAR(255) NULL,
                `robots_index` TINYINT(1) NOT NULL DEFAULT 1,
                `view_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                `created_by` BIGINT UNSIGNED NULL,
                `updated_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                KEY `key_blog_posts_author_id` (`author_id`),
                KEY `key_blog_posts_status_dates` (`status`, `published_at`, `scheduled_at`),
                KEY `key_blog_posts_featured_status` (`is_featured`, `status`),
                KEY `key_blog_posts_deleted_at` (`deleted_at`),
                FULLTEXT KEY `fulltext_blog_posts_search` (`title`, `excerpt`, `content`),
                CONSTRAINT `blog_posts_author_id_fk`
                    FOREIGN KEY (`author_id`) REFERENCES `blog_authors` (`id`) ON DELETE SET NULL,
                CONSTRAINT `blog_posts_created_by_fk`
                    FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
                CONSTRAINT `blog_posts_updated_by_fk`
                    FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_post_categories` (
                `post_id` BIGINT UNSIGNED NOT NULL,
                `category_id` BIGINT UNSIGNED NOT NULL,
                `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`post_id`, `category_id`),
                KEY `key_blog_post_categories_category_id` (`category_id`),
                CONSTRAINT `blog_post_categories_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
                CONSTRAINT `blog_post_categories_category_id_fk`
                    FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_post_tags` (
                `post_id` BIGINT UNSIGNED NOT NULL,
                `tag_id` BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (`post_id`, `tag_id`),
                KEY `key_blog_post_tags_tag_id` (`tag_id`),
                CONSTRAINT `blog_post_tags_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
                CONSTRAINT `blog_post_tags_tag_id_fk`
                    FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_post_views` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `post_id` BIGINT UNSIGNED NOT NULL,
                `ip_hash` CHAR(64) NULL,
                `user_agent_hash` CHAR(64) NULL,
                `referrer` VARCHAR(255) NULL,
                `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_blog_post_views_post_id` (`post_id`),
                KEY `key_blog_post_views_viewed_at` (`viewed_at`),
                CONSTRAINT `blog_post_views_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_media` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `post_id` BIGINT UNSIGNED NULL,
                `media_library_id` BIGINT UNSIGNED NULL,
                `path` VARCHAR(255) NOT NULL,
                `alt_text` VARCHAR(255) NULL,
                `caption` VARCHAR(255) NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_blog_media_post_id` (`post_id`),
                KEY `key_blog_media_media_library_id` (`media_library_id`),
                CONSTRAINT `blog_media_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
                CONSTRAINT `blog_media_media_library_id_fk`
                    FOREIGN KEY (`media_library_id`) REFERENCES `media_library` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_revisions` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `post_id` BIGINT UNSIGNED NOT NULL,
                `admin_user_id` BIGINT UNSIGNED NULL,
                `title` VARCHAR(255) NOT NULL,
                `excerpt` TEXT NULL,
                `content` MEDIUMTEXT NOT NULL,
                `status` VARCHAR(30) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_blog_revisions_post_id` (`post_id`),
                CONSTRAINT `blog_revisions_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
                CONSTRAINT `blog_revisions_admin_user_id_fk`
                    FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_slug_redirects` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `post_id` BIGINT UNSIGNED NOT NULL,
                `old_slug` VARCHAR(255) NOT NULL UNIQUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_blog_slug_redirects_post_id` (`post_id`),
                CONSTRAINT `blog_slug_redirects_post_id_fk`
                    FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `blog_ad_placements` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `placement_key` VARCHAR(100) NOT NULL UNIQUE,
                `label` VARCHAR(160) NOT NULL,
                `description` VARCHAR(255) NULL,
                `display_context` VARCHAR(60) NOT NULL DEFAULT 'blog',
                `adsense_client` VARCHAR(80) NULL,
                `adsense_slot` VARCHAR(80) NULL,
                `ad_format` VARCHAR(40) NOT NULL DEFAULT 'auto',
                `reserved_height` INT NOT NULL DEFAULT 280,
                `min_word_count` INT NOT NULL DEFAULT 0,
                `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `key_blog_ad_placements_enabled` (`is_enabled`, `display_context`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $this->seedPermissions();
        $this->seedAdPlacements();
    }

    public function down(): void
    {
        $this->dropIfExists('blog_ad_placements');
        $this->dropIfExists('blog_slug_redirects');
        $this->dropIfExists('blog_revisions');
        $this->dropIfExists('blog_media');
        $this->dropIfExists('blog_post_views');
        $this->dropIfExists('blog_post_tags');
        $this->dropIfExists('blog_post_categories');
        $this->dropIfExists('blog_posts');
        $this->dropIfExists('blog_tags');
        $this->dropIfExists('blog_categories');
        $this->dropIfExists('blog_authors');
    }

    private function seedPermissions(): void
    {
        foreach ([
            'blog.view',
            'blog.create',
            'blog.edit',
            'blog.delete',
            'blog.publish',
            'blog.restore',
            'blog.taxonomy',
            'blog.ads',
        ] as $permission) {
            [$module] = explode('.', $permission);
            $this->connection->execute(
                "INSERT INTO permissions (name, slug, module)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE name = VALUES(name), module = VALUES(module)",
                [ucwords(str_replace(['.', '_'], ' ', $permission)), $permission, $module]
            );
        }
    }

    private function seedAdPlacements(): void
    {
        foreach ([
            ['blog_listing_between_sections', 'Blog listing mid-page', 'Between listing sections on /blog.', 'listing', 280, 0, 10],
            ['article_after_intro', 'Article below introduction', 'Below the article introduction, before the main body.', 'article', 250, 400, 20],
            ['article_in_content', 'Article in-content', 'Between long-form content sections only.', 'article', 280, 900, 30],
            ['article_sidebar', 'Article sidebar', 'Large-screen sidebar placement.', 'article', 300, 0, 40],
            ['article_before_related', 'Before related posts', 'Near the end of an article before related content.', 'article', 280, 700, 50],
        ] as $placement) {
            $this->connection->execute(
                "INSERT INTO blog_ad_placements (
                    placement_key, label, description, display_context, reserved_height, min_word_count, sort_order
                 ) VALUES (?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    label = VALUES(label),
                    description = VALUES(description),
                    display_context = VALUES(display_context),
                    reserved_height = VALUES(reserved_height),
                    min_word_count = VALUES(min_word_count),
                    sort_order = VALUES(sort_order)",
                $placement
            );
        }
    }
}
