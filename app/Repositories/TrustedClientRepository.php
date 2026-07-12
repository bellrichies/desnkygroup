<?php

namespace App\Repositories;

class TrustedClientRepository extends BaseRepository
{
    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM trusted_clients ORDER BY sort_order ASC, name ASC"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function active(?string $serviceSlug = null): array
    {
        if ($serviceSlug !== null) {
            return $this->connection->query(
                "SELECT * FROM trusted_clients
                 WHERE is_active = 1 AND (service_slug = ? OR service_slug IS NULL)
                 ORDER BY sort_order ASC, name ASC",
                [$serviceSlug]
            );
        }

        return $this->connection->query(
            "SELECT * FROM trusted_clients WHERE is_active = 1 ORDER BY sort_order ASC, name ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM trusted_clients WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO trusted_clients (name, logo, website_url, service_slug, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['logo'] ?? null,
                $data['website_url'] ?? null,
                $data['service_slug'] ?? null,
                (int) ($data['sort_order'] ?? 0),
                isset($data['is_active']) ? 1 : 0,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE trusted_clients
             SET name = ?, logo = ?, website_url = ?, service_slug = ?, sort_order = ?, is_active = ?
             WHERE id = ?",
            [
                $data['name'],
                $data['logo'] ?? null,
                $data['website_url'] ?? null,
                $data['service_slug'] ?? null,
                (int) ($data['sort_order'] ?? 0),
                isset($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM trusted_clients WHERE id = ?", [$id]);

        return true;
    }

    public function toggleActive(int $id): bool
    {
        $this->connection->update(
            "UPDATE trusted_clients SET is_active = 1 - is_active WHERE id = ?",
            [$id]
        );

        return true;
    }
}
