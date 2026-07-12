<?php

namespace App\Repositories;

/**
 * Data access for AdSense-safe blog ad placements.
 */
class BlogAdRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            'SELECT * FROM blog_ad_placements ORDER BY sort_order ASC, label ASC'
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function enabledForContext(string $context): array
    {
        $rows = $this->connection->query(
            "SELECT *
             FROM blog_ad_placements
             WHERE is_enabled = 1 AND display_context IN (?, 'blog')
             ORDER BY sort_order ASC",
            [$context]
        );

        $placements = [];
        foreach ($rows as $row) {
            $placements[(string) $row['placement_key']] = $row;
        }

        return $placements;
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne('SELECT * FROM blog_ad_placements WHERE id = ?', [$id]);
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE blog_ad_placements
             SET label = ?, description = ?, display_context = ?, adsense_client = ?, adsense_slot = ?,
                 ad_format = ?, reserved_height = ?, min_word_count = ?, is_enabled = ?, sort_order = ?
             WHERE id = ?",
            [
                $data['label'],
                $data['description'] ?? null,
                $data['display_context'] ?? 'blog',
                $data['adsense_client'] ?? null,
                $data['adsense_slot'] ?? null,
                $data['ad_format'] ?? 'auto',
                max(90, (int) ($data['reserved_height'] ?? 280)),
                max(0, (int) ($data['min_word_count'] ?? 0)),
                !empty($data['is_enabled']) ? 1 : 0,
                (int) ($data['sort_order'] ?? 0),
                $id,
            ]
        );

        return true;
    }
}
