<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Add cross-table constraints that require later foundation tables.
 */
class AddFoundationForeignKeys extends Migration
{
    public function up(): void
    {
        $this->addConstraint(
            'products',
            'products_category_id_fk',
            'ALTER TABLE `products`
             ADD CONSTRAINT `products_category_id_fk`
             FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE RESTRICT'
        );

        $this->addConstraint(
            'products',
            'products_created_by_fk',
            'ALTER TABLE `products`
             ADD CONSTRAINT `products_created_by_fk`
             FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE RESTRICT'
        );

        $this->addConstraint(
            'contacts',
            'contacts_assigned_to_fk',
            'ALTER TABLE `contacts`
             ADD CONSTRAINT `contacts_assigned_to_fk`
             FOREIGN KEY (`assigned_to`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL'
        );
    }

    public function down(): void
    {
        $this->dropConstraint('contacts', 'contacts_assigned_to_fk');
        $this->dropConstraint('products', 'products_created_by_fk');
        $this->dropConstraint('products', 'products_category_id_fk');
    }

    private function addConstraint(string $table, string $constraint, string $sql): void
    {
        if ($this->constraintExists($table, $constraint)) {
            return;
        }

        $this->connection->execute($sql);
    }

    private function dropConstraint(string $table, string $constraint): void
    {
        if (!$this->constraintExists($table, $constraint)) {
            return;
        }

        $this->connection->execute("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
    }

    private function constraintExists(string $table, string $constraint): bool
    {
        $result = $this->connection->queryOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?",
            [$table, $constraint]
        );

        return $result !== null;
    }
}
