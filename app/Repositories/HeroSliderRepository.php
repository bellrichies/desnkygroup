<?php

namespace App\Repositories;

/**
 * Data access for admin-managed homepage hero sliders.
 */
class HeroSliderRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM homepage_hero_sliders ORDER BY sort_order ASC, id ASC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function active(): array
    {
        return $this->connection->query(
            "SELECT * FROM homepage_hero_sliders
             WHERE is_active = 1
             ORDER BY sort_order ASC, id ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM homepage_hero_sliders WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO homepage_hero_sliders
                (background_image, heading, caption, primary_cta_label, primary_cta_url,
                 secondary_cta_label, secondary_cta_url, sort_order, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['background_image'],
                $data['heading'],
                $data['caption'] ?? null,
                $data['primary_cta_label'] ?? null,
                $data['primary_cta_url'] ?? null,
                $data['secondary_cta_label'] ?? null,
                $data['secondary_cta_url'] ?? null,
                (int) ($data['sort_order'] ?? 0),
                !empty($data['is_active']) ? 1 : 0,
                $data['created_by'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE homepage_hero_sliders
             SET background_image = ?, heading = ?, caption = ?, primary_cta_label = ?,
                 primary_cta_url = ?, secondary_cta_label = ?, secondary_cta_url = ?,
                 sort_order = ?, is_active = ?
             WHERE id = ?",
            [
                $data['background_image'],
                $data['heading'],
                $data['caption'] ?? null,
                $data['primary_cta_label'] ?? null,
                $data['primary_cta_url'] ?? null,
                $data['secondary_cta_label'] ?? null,
                $data['secondary_cta_url'] ?? null,
                (int) ($data['sort_order'] ?? 0),
                !empty($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete(
            "DELETE FROM homepage_hero_sliders WHERE id = ?",
            [$id]
        );

        return true;
    }

    public function toggleActive(int $id): bool
    {
        $this->connection->update(
            "UPDATE homepage_hero_sliders SET is_active = 1 - is_active WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * @param array<int, int> $orders keyed by slider id.
     */
    public function updateSortOrders(array $orders): bool
    {
        foreach ($orders as $id => $sortOrder) {
            $this->connection->update(
                "UPDATE homepage_hero_sliders SET sort_order = ? WHERE id = ?",
                [(int) $sortOrder, (int) $id]
            );
        }

        return true;
    }
}
