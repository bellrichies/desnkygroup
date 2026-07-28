<?php

namespace App\Repositories;

/**
 * Data access for admin users.
 */
class AdminUserRepository extends BaseRepository
{
    /**
     * Find an active admin user by email.
     *
     * @param string $email Email address.
     * @return array<string, mixed>|null
     */
    public function findActiveByEmail(string $email): ?array
    {
        return $this->connection->queryOne(
            "SELECT id, full_name, email, password_hash, role, is_active, last_login_at
             FROM admin_users
             WHERE email = ? AND is_active = 1 AND deleted_at IS NULL
             LIMIT 1",
            [$email]
        );
    }

    /**
     * Update the last login timestamp.
     *
     * @param int $id Admin user ID.
     * @return void
     */
    public function updateLastLogin(int $id): void
    {
        $this->connection->update(
            "UPDATE admin_users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?",
            [$_SERVER['REMOTE_ADDR'] ?? null, $id]
        );
    }

    /**
     * Count all active admin users.
     *
     * @return int
     */
    public function countActive(): int
    {
        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate FROM admin_users WHERE is_active = 1 AND deleted_at IS NULL"
        );

        return (int) ($row['aggregate'] ?? 0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(?string $search = null): array
    {
        $params = [];
        $where = 'u.deleted_at IS NULL';

        if ($search !== null && trim($search) !== '') {
            $where .= ' AND (u.full_name LIKE ? OR u.email LIKE ?)';
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
        }

        return $this->connection->query(
            "SELECT u.*,
                    GROUP_CONCAT(r.name ORDER BY r.sort_order, r.name SEPARATOR ', ') AS role_names
             FROM admin_users u
             LEFT JOIN admin_user_roles ur ON ur.admin_user_id = u.id
             LEFT JOIN roles r ON r.id = ur.role_id
             WHERE {$where}
             GROUP BY u.id
             ORDER BY u.created_at DESC",
            $params
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM admin_users WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO admin_users (
                full_name, email, password_hash, role, is_active, force_password_reset
            ) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['full_name'],
                $data['email'],
                $data['password_hash'],
                $data['role'] ?? 'editor',
                !empty($data['is_active']) ? 1 : 0,
                !empty($data['force_password_reset']) ? 1 : 0,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE admin_users
             SET full_name = ?, email = ?, role = ?, is_active = ?,
                 suspended_at = CASE WHEN ? = 1 THEN NULL ELSE COALESCE(suspended_at, NOW()) END
             WHERE id = ?",
            [
                $data['full_name'],
                $data['email'],
                $data['role'] ?? 'editor',
                !empty($data['is_active']) ? 1 : 0,
                !empty($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function softDelete(int $id): bool
    {
        $this->connection->update(
            "UPDATE admin_users SET deleted_at = NOW(), is_active = 0 WHERE id = ?",
            [$id]
        );

        return true;
    }

    public function setPassword(int $id, string $passwordHash, bool $forceReset): bool
    {
        $this->connection->update(
            "UPDATE admin_users
             SET password_hash = ?, password_changed_at = NOW(), force_password_reset = ?
             WHERE id = ?",
            [$passwordHash, $forceReset ? 1 : 0, $id]
        );

        return true;
    }

    /**
     * @param array<int, int> $roleIds Role IDs.
     */
    public function syncRoles(int $adminUserId, array $roleIds): void
    {
        $this->connection->delete('DELETE FROM admin_user_roles WHERE admin_user_id = ?', [$adminUserId]);

        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            if ($roleId <= 0) {
                continue;
            }

            $this->connection->insert(
                'INSERT IGNORE INTO admin_user_roles (admin_user_id, role_id) VALUES (?, ?)',
                [$adminUserId, $roleId]
            );
        }
    }

    /**
     * @return array<int, int>
     */
    public function roleIds(int $adminUserId): array
    {
        $rows = $this->connection->query(
            'SELECT role_id FROM admin_user_roles WHERE admin_user_id = ?',
            [$adminUserId]
        );

        return array_map('intval', array_column($rows, 'role_id'));
    }

    /**
     * @return array<int, string>
     */
    public function roleSlugs(int $adminUserId): array
    {
        $rows = $this->connection->query(
            "SELECT r.slug
             FROM roles r
             INNER JOIN admin_user_roles ur ON ur.role_id = r.id
             WHERE ur.admin_user_id = ?",
            [$adminUserId]
        );

        return array_map('strval', array_column($rows, 'slug'));
    }

    /**
     * @return array<int, string>
     */
    public function permissionSlugs(int $adminUserId): array
    {
        $rows = $this->connection->query(
            "SELECT DISTINCT p.slug
             FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             INNER JOIN admin_user_roles ur ON ur.role_id = rp.role_id
             WHERE ur.admin_user_id = ?",
            [$adminUserId]
        );

        return array_map('strval', array_column($rows, 'slug'));
    }

    public function countSuperAdmins(?int $exceptUserId = null): int
    {
        $params = [];
        $where = "r.slug = 'super-admin' AND u.is_active = 1 AND u.deleted_at IS NULL";

        if ($exceptUserId !== null) {
            $where .= ' AND u.id <> ?';
            $params[] = $exceptUserId;
        }

        $row = $this->connection->queryOne(
            "SELECT COUNT(DISTINCT u.id) AS aggregate
             FROM admin_users u
             INNER JOIN admin_user_roles ur ON ur.admin_user_id = u.id
             INNER JOIN roles r ON r.id = ur.role_id
             WHERE {$where}",
            $params
        );

        return (int) ($row['aggregate'] ?? 0);
    }

    public function rememberSession(int $adminUserId, string $sessionId): void
    {
        $this->connection->insert(
            "INSERT INTO admin_sessions (admin_user_id, session_id, ip_address, user_agent)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE last_activity_at = NOW()",
            [
                $adminUserId,
                $sessionId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]
        );
    }

    public function removeSession(string $sessionId): void
    {
        $this->connection->delete('DELETE FROM admin_sessions WHERE session_id = ?', [$sessionId]);
    }
}
