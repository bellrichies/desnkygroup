<?php

namespace App\Repositories;

/**
 * Data access for published projects.
 */
class ProjectRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM projects WHERE deleted_at IS NULL ORDER BY sort_order ASC, created_at DESC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM projects WHERE id = ? AND deleted_at IS NULL", [$id]);
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        return $this->connection->queryOne("SELECT *
             FROM projects
             WHERE slug = ? AND is_published = 1 AND deleted_at IS NULL
             LIMIT 1", [$slug]);
    }
    /**
     * @return array<int, array>
     */
    public function published(): array
    {
        return $this->connection->query(
            "SELECT *
             FROM projects
             WHERE is_published = 1 AND deleted_at IS NULL
             ORDER BY sort_order ASC, created_at DESC"
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO projects
                (title, slug, summary, description, category, client_name, project_date, featured_image,
                 is_published, sort_order, meta_title, meta_description, meta_keywords, canonical_url, og_image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['title'],
                $data['slug'],
                $data['summary'] ?? null,
                $data['description'] ?? null,
                $data['category'] ?? null,
                $data['client_name'] ?? null,
                $data['project_date'] ?: null,
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
            "UPDATE projects
             SET title = ?, slug = ?, summary = ?, description = ?, category = ?, client_name = ?,
                 project_date = ?, featured_image = ?, is_published = ?, sort_order = ?, meta_title = ?,
                 meta_description = ?, meta_keywords = ?, canonical_url = ?, og_image = ?
             WHERE id = ?",
            [
                $data['title'],
                $data['slug'],
                $data['summary'] ?? null,
                $data['description'] ?? null,
                $data['category'] ?? null,
                $data['client_name'] ?? null,
                $data['project_date'] ?: null,
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
        $this->connection->update("UPDATE projects SET deleted_at = NOW() WHERE id = ?", [$id]);

        return true;
    }

    /** @return array<int, array<string, mixed>> */
    public function galleryImages(int $projectId): array
    {
        return $this->connection->query(
            "SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC",
            [$projectId]
        );
    }

    public function syncGallery(int $projectId, array $images): void
    {
        $this->connection->delete("DELETE FROM project_images WHERE project_id = ?", [$projectId]);

        foreach ($images as $i => $img) {
            $path = trim((string) ($img['path'] ?? ''));
            if ($path === '') {
                continue;
            }
            $this->connection->insert(
                "INSERT INTO project_images (project_id, path, alt_text, sort_order) VALUES (?, ?, ?, ?)",
                [$projectId, $path, $img['alt_text'] ?? null, (int) ($img['sort_order'] ?? $i)]
            );
        }
    }
}
