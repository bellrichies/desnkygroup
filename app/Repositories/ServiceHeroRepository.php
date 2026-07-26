<?php

namespace App\Repositories;

class ServiceHeroRepository extends BaseRepository
{
    public function forService(int $serviceId): array
    {
        return $this->connection->query(
            "SELECT * FROM service_hero_slides WHERE service_id = ? ORDER BY sort_order ASC, id ASC",
            [$serviceId]
        );
    }

    public function activeForService(int $serviceId): array
    {
        return $this->connection->query(
            "SELECT * FROM service_hero_slides
             WHERE service_id = ? AND is_visible = 1 AND is_active = 1
             ORDER BY sort_order ASC, id ASC",
            [$serviceId]
        );
    }

    public function find(int $id, int $serviceId): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM service_hero_slides WHERE id = ? AND service_id = ?",
            [$id, $serviceId]
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO service_hero_slides
                (service_id, heading, subheading, description, media_type, background_media,
                 primary_cta_label, primary_cta_url, secondary_cta_label, secondary_cta_url,
                 sort_order, is_visible, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            $this->values($data, true)
        );
    }

    public function update(int $id, int $serviceId, array $data): bool
    {
        $values = $this->values($data, false);
        $values[] = $id;
        $values[] = $serviceId;
        $this->connection->update(
            "UPDATE service_hero_slides SET
                heading = ?, subheading = ?, description = ?, media_type = ?, background_media = ?,
                primary_cta_label = ?, primary_cta_url = ?, secondary_cta_label = ?,
                secondary_cta_url = ?, sort_order = ?, is_visible = ?, is_active = ?
             WHERE id = ? AND service_id = ?",
            $values
        );

        return true;
    }

    public function delete(int $id, int $serviceId): bool
    {
        $this->connection->delete(
            "DELETE FROM service_hero_slides WHERE id = ? AND service_id = ?",
            [$id, $serviceId]
        );

        return true;
    }

    public function toggle(int $id, int $serviceId): bool
    {
        $this->connection->update(
            "UPDATE service_hero_slides SET is_active = 1 - is_active WHERE id = ? AND service_id = ?",
            [$id, $serviceId]
        );

        return true;
    }

    public function reorder(int $serviceId, array $orders): bool
    {
        foreach ($orders as $id => $order) {
            $this->connection->update(
                "UPDATE service_hero_slides SET sort_order = ? WHERE id = ? AND service_id = ?",
                [(int) $order, (int) $id, $serviceId]
            );
        }

        return true;
    }

    private function values(array $data, bool $withService): array
    {
        $values = [
            $data['heading'],
            $data['subheading'],
            $data['description'],
            $data['media_type'],
            $data['background_media'],
            $data['primary_cta_label'],
            $data['primary_cta_url'],
            $data['secondary_cta_label'],
            $data['secondary_cta_url'],
            $data['sort_order'],
            $data['is_visible'],
            $data['is_active'],
        ];

        if (!$withService) {
            return $values;
        }

        array_unshift($values, $data['service_id']);
        $values[] = $data['created_by'];

        return $values;
    }
}
