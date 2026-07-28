<?php

namespace App\Repositories;

/**
 * Data access layer for service content.
 */
class ServiceRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM services WHERE deleted_at IS NULL ORDER BY sort_order ASC, created_at DESC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM services WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM services WHERE slug = ? AND is_published = 1 AND deleted_at IS NULL",
            [$slug]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function published(): array
    {
        return $this->connection->query(
            "SELECT * FROM services WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC, title ASC"
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO services
                (title, slug, summary, content, icon, category, featured_image, is_published, sort_order,
                 meta_title, meta_description, meta_keywords, canonical_url, og_image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['slug'],
                $data['summary'] ?? null,
                $data['content'] ?? null,
                $data['icon'] ?? null,
                $data['category'] ?? null,
                $data['featured_image'] ?? null,
                !empty($data['is_published']) ? 1 : 0,
                (int) ($data['sort_order'] ?? 0),
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['canonical_url'] ?? null,
                $data['og_image'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE services
             SET title = ?, slug = ?, summary = ?, content = ?, icon = ?, category = ?, featured_image = ?,
                 is_published = ?, sort_order = ?, meta_title = ?, meta_description = ?, meta_keywords = ?,
                 canonical_url = ?, og_image = ?
             WHERE id = ?",
            [
                $data['title'],
                $data['slug'],
                $data['summary'] ?? null,
                $data['content'] ?? null,
                $data['icon'] ?? null,
                $data['category'] ?? null,
                $data['featured_image'] ?? null,
                !empty($data['is_published']) ? 1 : 0,
                (int) ($data['sort_order'] ?? 0),
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['canonical_url'] ?? null,
                $data['og_image'] ?? null,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->update("UPDATE services SET deleted_at = NOW() WHERE id = ?", [$id]);

        return true;
    }
}
