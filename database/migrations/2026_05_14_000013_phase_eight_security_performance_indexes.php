<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Add Phase 8 indexes used by admin filtering and public catalogue reads.
 */
class PhaseEightSecurityPerformanceIndexes extends Migration
{
    public function up(): void
    {
        $this->addIndex('contacts', 'key_contacts_status_created_at', ['status', 'created_at']);
        $this->addIndex('contacts', 'key_contacts_email_created_at', ['email', 'created_at']);
        $this->addIndex('newsletter_subscribers', 'key_newsletter_status_created_at', ['status', 'created_at']);
        $this->addIndex('services', 'key_services_published_sort_deleted', [
            'is_published',
            'sort_order',
            'deleted_at',
        ]);
        $this->addIndex('projects', 'key_projects_published_sort_deleted', [
            'is_published',
            'sort_order',
            'deleted_at',
        ]);
        $this->addIndex('products', 'key_products_published_category_deleted', [
            'is_published',
            'category_id',
            'deleted_at',
        ]);
        $this->addIndex('orders', 'key_orders_status_created_at', ['status', 'created_at']);
        $this->addIndex('media_library', 'key_media_created_at', ['created_at']);
    }

    public function down(): void
    {
        $this->dropIndex('contacts', 'key_contacts_status_created_at');
        $this->dropIndex('contacts', 'key_contacts_email_created_at');
        $this->dropIndex('newsletter_subscribers', 'key_newsletter_status_created_at');
        $this->dropIndex('services', 'key_services_published_sort_deleted');
        $this->dropIndex('projects', 'key_projects_published_sort_deleted');
        $this->dropIndex('products', 'key_products_published_category_deleted');
        $this->dropIndex('orders', 'key_orders_status_created_at');
        $this->dropIndex('media_library', 'key_media_created_at');
    }

    /**
     * @param array<int, string> $columns
     */
    private function addIndex(string $table, string $index, array $columns): void
    {
        if ($this->indexExists($table, $index) || !$this->tableExists($table) || !$this->columnsExist($table, $columns)) {
            return;
        }

        $columnList = '`' . implode('`, `', $columns) . '`';
        $this->connection->execute("ALTER TABLE `{$table}` ADD INDEX `{$index}` ({$columnList})");
    }

    private function dropIndex(string $table, string $index): void
    {
        if (!$this->tableExists($table) || !$this->indexExists($table, $index)) {
            return;
        }

        $this->connection->execute("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
    }

    private function tableExists(string $table): bool
    {
        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$table]
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

    /**
     * @param array<int, string> $columns
     */
    private function columnsExist(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            $row = $this->connection->queryOne(
                "SELECT COUNT(*) AS aggregate
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                [$table, $column]
            );

            if ((int) ($row['aggregate'] ?? 0) === 0) {
                return false;
            }
        }

        return true;
    }
}
