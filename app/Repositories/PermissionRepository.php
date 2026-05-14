<?php

namespace App\Repositories;

/**
 * Data access for permissions.
 */
class PermissionRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT p.*, parent.name AS parent_name
             FROM permissions p
             LEFT JOIN permissions parent ON parent.id = p.parent_id
             ORDER BY p.module ASC, p.slug ASC"
        );
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function grouped(): array
    {
        $grouped = [];

        foreach ($this->all() as $permission) {
            $grouped[(string) $permission['module']][] = $permission;
        }

        return $grouped;
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne('SELECT * FROM permissions WHERE id = ?', [$id]);
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO permissions (name, slug, module, parent_id, description)
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['module'],
                $data['parent_id'] ?: null,
                $data['description'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE permissions SET name = ?, slug = ?, module = ?, parent_id = ?, description = ? WHERE id = ?",
            [
                $data['name'],
                $data['slug'],
                $data['module'],
                $data['parent_id'] ?: null,
                $data['description'] ?? null,
                $id,
            ]
        );

        return true;
    }
}
