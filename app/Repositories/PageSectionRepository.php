<?php

namespace App\Repositories;

/**
 * Data access for CMS page sections.
 */
class PageSectionRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function forPage(int $pageId): array
    {
        return $this->connection->query(
            "SELECT *
             FROM page_sections
             WHERE page_id = ?
             ORDER BY sort_order ASC, id ASC",
            [$pageId]
        );
    }
}
