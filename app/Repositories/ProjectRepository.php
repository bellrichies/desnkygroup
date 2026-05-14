<?php

namespace App\Repositories;

/**
 * Data access for published projects.
 */
class ProjectRepository extends BaseRepository
{
    /**
     * @return array<int, array>
     */
    public function published(): array
    {
        return $this->connection->query(
            "SELECT title, slug, summary, description, category, featured_image
             FROM projects
             WHERE is_published = 1 AND deleted_at IS NULL
             ORDER BY created_at DESC"
        );
    }
}
