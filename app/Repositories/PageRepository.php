<?php

namespace App\Repositories;

/**
 * PageRepository - Data access layer for pages
 */
class PageRepository extends BaseRepository
{
    /**
     * Get all pages
     *
     * @return array
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM pages WHERE deleted_at IS NULL ORDER BY created_at DESC"
        );
    }

    /**
     * Get published pages only
     *
     * @return array
     */
    public function published(): array
    {
        return $this->connection->query(
            "SELECT * FROM pages WHERE is_published = true AND deleted_at IS NULL ORDER BY created_at DESC"
        );
    }

    /**
     * Find page by ID
     *
     * @param int $id Page ID
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM pages WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find page by slug
     *
     * @param string $slug Page slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM pages WHERE slug = ? AND deleted_at IS NULL",
            [$slug]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find published page by slug.
     *
     * @param string $slug Page slug
     * @return array|null
     */
    public function findPublishedBySlug(string $slug): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM pages WHERE slug = ? AND is_published = true AND deleted_at IS NULL",
            [$slug]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Save a new page
     *
     * @param array $data Page data
     * @return int Page ID
     */
    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO pages
                (title, slug, content, excerpt, meta_title, meta_description, meta_keywords,
                 featured_image, is_published, published_at, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'] ?? '',
                $data['slug'] ?? '',
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['featured_image'] ?? null,
                !empty($data['is_published']) ? 1 : 0,
                !empty($data['is_published']) ? date('Y-m-d H:i:s') : null,
                $data['created_by'] ?? 1,
            ]
        );
    }

    /**
     * Update an existing page
     *
     * @param int $id Page ID
     * @param array $data Updated data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE pages
             SET title = ?, slug = ?, content = ?, excerpt = ?, meta_title = ?, meta_description = ?,
                 meta_keywords = ?, featured_image = ?, is_published = ?,
                 published_at = CASE WHEN ? = 1 AND published_at IS NULL THEN NOW() ELSE published_at END
             WHERE id = ?",
            [
                $data['title'] ?? '',
                $data['slug'] ?? '',
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['featured_image'] ?? null,
                !empty($data['is_published']) ? 1 : 0,
                !empty($data['is_published']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    /**
     * Delete a page
     *
     * @param int $id Page ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->connection->delete(
            "DELETE FROM pages WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * Soft delete a page
     *
     * @param int $id Page ID
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $this->connection->update(
            "UPDATE pages SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * Publish a page
     *
     * @param int $id Page ID
     * @param int $userId Publishing user ID
     * @return bool
     */
    public function publish(int $id, int $userId = 1): bool
    {
        $this->connection->update(
            "UPDATE pages SET is_published = true, published_at = NOW(), published_by = ? WHERE id = ?",
            [$userId, $id]
        );

        return true;
    }
}
