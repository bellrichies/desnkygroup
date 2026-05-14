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
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN product_categories c ON c.id = p.category_id
             WHERE p.deleted_at IS NULL
             ORDER BY p.created_at DESC"
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
     * Get active products with category and primary image data.
     *
     * @return array
     */
    public function activeForShop(): array
    {
        return $this->connection->query(
            "SELECT
                p.*,
                c.name AS category_name,
                c.slug AS category_slug,
                COALESCE(pi.path, p.featured_image) AS display_image
             FROM products p
             LEFT JOIN product_categories c ON c.id = p.category_id
             LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.sort_order = (
                SELECT MIN(pi2.sort_order) FROM product_images pi2 WHERE pi2.product_id = p.id
             )
             WHERE p.is_active = 1 AND p.status = 'active' AND p.deleted_at IS NULL
             ORDER BY p.name ASC"
        );
    }

    /**
     * Get active product categories.
     *
     * @return array
     */
    public function activeCategories(): array
    {
        return $this->connection->query(
            'SELECT id, name, slug FROM product_categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC'
        );
    }

    /**
     * Get ordered gallery images for a product.
     *
     * @return array<int, array<string, mixed>>
     */
    public function imagesForProduct(int $productId): array
    {
        return $this->connection->query(
            "SELECT id, product_id, path, alt_text, sort_order
             FROM product_images
             WHERE product_id = ?
             ORDER BY sort_order ASC, id ASC",
            [$productId]
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
            "SELECT * FROM products WHERE id = ? AND deleted_at IS NULL",
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
            "SELECT * FROM products WHERE id = ? AND is_active = true AND status = 'active' AND deleted_at IS NULL",
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
            "SELECT * FROM products WHERE slug = ? AND deleted_at IS NULL",
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
            "SELECT * FROM products WHERE slug = ? AND is_active = true AND status = 'active' AND deleted_at IS NULL",
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
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN product_categories c ON c.id = p.category_id
             WHERE p.deleted_at IS NULL
             AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
             ORDER BY p.name ASC",
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
            "SELECT * FROM products
             WHERE category_id = ? AND is_active = true AND status = 'active' AND deleted_at IS NULL
             ORDER BY name ASC",
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
            "SELECT * FROM products
             WHERE quantity_in_stock <= reorder_level AND is_active = 1 AND deleted_at IS NULL
             ORDER BY quantity_in_stock ASC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function filterAdmin(array $filters = []): array
    {
        $where = ['p.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
            $search = '%' . $filters['q'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        return $this->connection->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN product_categories c ON c.id = p.category_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY p.created_at DESC",
            $params
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
            "INSERT INTO products (
                name, slug, description, short_description, price, discount_price, cost_price,
                quantity_in_stock, reorder_level, sku, weight, category_id, featured_image,
                meta_title, meta_description, is_active, status, is_featured, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['description'] ?? '',
                $data['short_description'] ?? null,
                $data['price'] ?? 0,
                $data['discount_price'] ?: null,
                $data['cost_price'] ?? null,
                $data['quantity_in_stock'] ?? 0,
                $data['reorder_level'] ?? 10,
                $data['sku'] ?? '',
                $data['weight'] ?: null,
                $data['category_id'] ?? null,
                $data['featured_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                $data['status'] ?? 'active',
                !empty($data['is_featured']) ? 1 : 0,
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
            "UPDATE products
             SET name = ?, slug = ?, description = ?, short_description = ?, price = ?,
                 discount_price = ?, cost_price = ?, quantity_in_stock = ?, reorder_level = ?,
                 sku = ?, weight = ?, category_id = ?, featured_image = ?, meta_title = ?,
                 meta_description = ?, is_active = ?, status = ?, is_featured = ?
             WHERE id = ?",
            [
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['description'] ?? '',
                $data['short_description'] ?? null,
                $data['price'] ?? 0,
                $data['discount_price'] ?: null,
                $data['cost_price'] ?: null,
                $data['quantity_in_stock'] ?? 0,
                $data['reorder_level'] ?? 10,
                $data['sku'] ?? '',
                $data['weight'] ?: null,
                $data['category_id'] ?? null,
                $data['featured_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                $data['status'] ?? 'active',
                !empty($data['is_featured']) ? 1 : 0,
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
        $current = $this->find($id);
        $this->connection->update(
            "UPDATE products SET quantity_in_stock = ? WHERE id = ?",
            [$quantity, $id]
        );

        if ($current !== null) {
            $this->recordInventoryMovement(
                $id,
                'manual_adjustment',
                $quantity - (int) $current['quantity_in_stock'],
                $quantity,
                null,
                'Manual inventory update'
            );
        }

        return true;
    }

    public function decrementStock(int $id, int $quantity, string $reference): bool
    {
        $affected = $this->connection->update(
            "UPDATE products
             SET quantity_in_stock = quantity_in_stock - ?
             WHERE id = ? AND quantity_in_stock >= ? AND deleted_at IS NULL",
            [$quantity, $id, $quantity]
        );

        if ($affected > 0) {
            $product = $this->find($id);
            $this->recordInventoryMovement(
                $id,
                'order',
                -$quantity,
                (int) ($product['quantity_in_stock'] ?? 0),
                $reference,
                'Stock reserved for order'
            );
        }

        return $affected > 0;
    }

    public function recordInventoryMovement(
        int $productId,
        string $type,
        int $change,
        int $quantityAfter,
        ?string $reference = null,
        ?string $notes = null
    ): void {
        $this->connection->insert(
            "INSERT INTO inventory_movements (
                product_id, movement_type, quantity_change, quantity_after, reference, notes
            ) VALUES (?, ?, ?, ?, ?, ?)",
            [$productId, $type, $change, $quantityAfter, $reference, $notes]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inventoryHistory(int $productId): array
    {
        return $this->connection->query(
            "SELECT * FROM inventory_movements
             WHERE product_id = ?
             ORDER BY created_at DESC, id DESC
             LIMIT 50",
            [$productId]
        );
    }
}
