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
            "SELECT * FROM pages ORDER BY created_at DESC"
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
            "SELECT * FROM pages WHERE is_published = true ORDER BY created_at DESC"
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
            "SELECT * FROM pages WHERE id = ?",
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
            "SELECT * FROM pages WHERE slug = ?",
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
            "SELECT * FROM pages WHERE slug = ? AND is_published = true",
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
            "INSERT INTO pages (title, slug, content, excerpt, meta_title, meta_description, created_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'] ?? '',
                $data['slug'] ?? '',
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
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
             SET title = ?, slug = ?, content = ?, excerpt = ?, meta_title = ?, meta_description = ?
             WHERE id = ?",
            [
                $data['title'] ?? '',
                $data['slug'] ?? '',
                $data['content'] ?? '',
                $data['excerpt'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
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
