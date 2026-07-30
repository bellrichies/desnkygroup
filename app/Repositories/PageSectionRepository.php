<?php

namespace App\Repositories;

/**
 * Data access for CMS page sections.
 */
class PageSectionRepository extends BaseRepository
{
    public function findByPageAndKey(int $pageId, string $sectionKey): ?array
    {
        return $this->connection->queryOne("SELECT * FROM page_sections WHERE page_id = ? AND section_key = ? LIMIT 1", [$pageId, $sectionKey]);
    }

    public function updateBody(int $id, string $body): void
    {
        $this->connection->update("UPDATE page_sections SET body = ?, updated_at = NOW() WHERE id = ?", [$body, $id]);
    }

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
