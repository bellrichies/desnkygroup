<?php

namespace App\Repositories;

/**
 * Data access for admin roles.
 */
class RoleRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT r.*, COUNT(DISTINCT ur.admin_user_id) AS users_count
             FROM roles r
             LEFT JOIN admin_user_roles ur ON ur.role_id = r.id
             GROUP BY r.id
             ORDER BY r.sort_order ASC, r.name ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne('SELECT * FROM roles WHERE id = ?', [$id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->connection->queryOne('SELECT * FROM roles WHERE slug = ?', [$slug]);
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO roles (name, slug, description, is_system_role, sort_order)
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                !empty($data['is_system_role']) ? 1 : 0,
                $data['sort_order'] ?? 0,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE roles SET name = ?, slug = ?, description = ?, sort_order = ? WHERE id = ?",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['sort_order'] ?? 0,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete('DELETE FROM roles WHERE id = ?', [$id]);

        return true;
    }

    /**
     * @param array<int, int> $permissionIds Permission IDs.
     */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->connection->delete('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);

        foreach (array_unique(array_map('intval', $permissionIds)) as $permissionId) {
            if ($permissionId <= 0) {
                continue;
            }

            $this->connection->insert(
                'INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)',
                [$roleId, $permissionId]
            );
        }
    }

    /**
     * @return array<int, int>
     */
    public function permissionIds(int $roleId): array
    {
        $rows = $this->connection->query(
            'SELECT permission_id FROM role_permissions WHERE role_id = ?',
            [$roleId]
        );

        return array_map('intval', array_column($rows, 'permission_id'));
    }
}
