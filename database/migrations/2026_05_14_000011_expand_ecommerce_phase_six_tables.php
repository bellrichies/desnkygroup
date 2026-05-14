<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Expand ecommerce tables with Phase 6 management fields.
 */
class ExpandEcommercePhaseSixTables extends Migration
{
    public function up(): void
    {
        $this->addColumn('product_categories', 'parent_id', '`parent_id` BIGINT UNSIGNED NULL AFTER `description`');
        $this->addColumn('product_categories', 'image', '`image` VARCHAR(255) NULL AFTER `parent_id`');
        $this->addColumn('product_categories', 'sort_order', '`sort_order` INT NOT NULL DEFAULT 0 AFTER `image`');
        $this->addColumn('product_categories', 'meta_title', '`meta_title` VARCHAR(255) NULL AFTER `sort_order`');
        $this->addColumn('product_categories', 'meta_description', '`meta_description` TEXT NULL AFTER `meta_title`');
        $this->addIndex('product_categories', 'key_product_categories_parent_id', '`parent_id`');
        $this->addIndex('product_categories', 'key_product_categories_sort_order', '`sort_order`');

        $this->addColumn('products', 'discount_price', '`discount_price` DECIMAL(10,2) NULL AFTER `price`');
        $this->addColumn('products', 'status', "`status` VARCHAR(50) NOT NULL DEFAULT 'active' AFTER `is_active`");
        $this->addColumn('products', 'is_featured', '`is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`');
        $this->addColumn('products', 'weight', '`weight` DECIMAL(10,2) NULL AFTER `sku`');
        $this->addIndex('products', 'key_products_status', '`status`');
        $this->addIndex('products', 'key_products_is_featured', '`is_featured`');

        $this->connection->execute("
            CREATE TABLE IF NOT EXISTS `inventory_movements` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `movement_type` VARCHAR(50) NOT NULL,
                `quantity_change` INT NOT NULL,
                `quantity_after` INT NOT NULL,
                `reference` VARCHAR(150) NULL,
                `notes` TEXT NULL,
                `created_by` BIGINT UNSIGNED NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `key_inventory_movements_product_id` (`product_id`),
                CONSTRAINT `inventory_movements_product_id_fk`
                    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        $this->dropIfExists('inventory_movements');
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
