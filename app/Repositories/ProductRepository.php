<?php

namespace App\Repositories;

/**
 * ProductRepository - Data access layer for products
 */
class ProductRepository extends BaseRepository
{
    /**
     * Get all products
     *
     * @return array
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM products ORDER BY name ASC"
        );
    }

    /**
     * Get active products only
     *
     * @return array
     */
    public function active(): array
    {
        return $this->connection->query(
            "SELECT * FROM products WHERE is_active = true ORDER BY name ASC"
        );
    }

    /**
     * Find product by ID
     *
     * @param int $id Product ID
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM products WHERE id = ?",
            [$id]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find active product by ID.
     *
     * @param int $id Product ID
     * @return array|null
     */
    public function findActive(int $id): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM products WHERE id = ? AND is_active = true",
            [$id]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find product by slug
     *
     * @param string $slug Product slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM products WHERE slug = ?",
            [$slug]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find active product by slug.
     *
     * @param string $slug Product slug
     * @return array|null
     */
    public function findActiveBySlug(string $slug): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM products WHERE slug = ? AND is_active = true",
            [$slug]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find product by SKU
     *
     * @param string $sku Product SKU
     * @return array|null
     */
    public function findBySku(string $sku): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM products WHERE sku = ?",
            [$sku]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Search products
     *
     * @param string $query Search query
     * @return array
     */
    public function search(string $query): array
    {
        $searchTerm = "%{$query}%";

        return $this->connection->query(
            "SELECT * FROM products 
             WHERE is_active = true 
             AND (name LIKE ? OR description LIKE ? OR sku LIKE ?)
             ORDER BY name ASC",
            [$searchTerm, $searchTerm, $searchTerm]
        );
    }

    /**
     * Get products by category
     *
     * @param int $categoryId Category ID
     * @return array
     */
    public function byCategory(int $categoryId): array
    {
        return $this->connection->query(
            "SELECT * FROM products WHERE category_id = ? AND is_active = true ORDER BY name ASC",
            [$categoryId]
        );
    }

    /**
     * Get low stock products
     *
     * @return array
     */
    public function lowStock(): array
    {
        return $this->connection->query(
            "SELECT * FROM products WHERE quantity_in_stock <= reorder_level ORDER BY quantity_in_stock ASC"
        );
    }

    /**
     * Create a new product
     *
     * @param array $data Product data
     * @return int Product ID
     */
    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO products (name, slug, description, short_description, price, cost_price, 
             quantity_in_stock, reorder_level, sku, category_id, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['description'] ?? '',
                $data['short_description'] ?? null,
                $data['price'] ?? 0,
                $data['cost_price'] ?? null,
                $data['quantity_in_stock'] ?? 0,
                $data['reorder_level'] ?? 10,
                $data['sku'] ?? '',
                $data['category_id'] ?? null,
                $data['created_by'] ?? 1,
            ]
        );
    }

    /**
     * Update a product
     *
     * @param int $id Product ID
     * @param array $data Updated data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE products SET name = ?, slug = ?, description = ?, price = ?, quantity_in_stock = ? WHERE id = ?",
            [
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['description'] ?? '',
                $data['price'] ?? 0,
                $data['quantity_in_stock'] ?? 0,
                $id,
            ]
        );

        return true;
    }

    /**
     * Delete a product
     *
     * @param int $id Product ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->connection->delete(
            "DELETE FROM products WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * Soft delete a product
     *
     * @param int $id Product ID
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $this->connection->update(
            "UPDATE products SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * Update product inventory
     *
     * @param int $id Product ID
     * @param int $quantity New quantity
     * @return bool
     */
    public function updateInventory(int $id, int $quantity): bool
    {
        $this->connection->update(
            "UPDATE products SET quantity_in_stock = ? WHERE id = ?",
            [$quantity, $id]
        );

        return true;
    }
}
