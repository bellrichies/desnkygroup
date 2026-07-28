<?php

namespace App\Repositories;

/**
 * Data access for ecommerce product categories.
 */
class ProductCategoryRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT c.*, p.name AS parent_name
             FROM product_categories c
             LEFT JOIN product_categories p ON p.id = c.parent_id
             ORDER BY c.sort_order ASC, c.name ASC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function active(): array
    {
        return $this->connection->query(
            "SELECT id, name, slug, parent_id
             FROM product_categories
             WHERE is_active = 1
             ORDER BY sort_order ASC, name ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM product_categories WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO product_categories (
                name, slug, description, parent_id, image, sort_order,
                meta_title, meta_description, is_active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['parent_id'] ?: null,
                $data['image'] ?? null,
                $data['sort_order'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE product_categories
             SET name = ?, slug = ?, description = ?, parent_id = ?, image = ?,
                 sort_order = ?, meta_title = ?, meta_description = ?, is_active = ?
             WHERE id = ?",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['parent_id'] ?: null,
                $data['image'] ?? null,
                $data['sort_order'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM product_categories WHERE id = ?", [$id]);

        return true;
    }
}
