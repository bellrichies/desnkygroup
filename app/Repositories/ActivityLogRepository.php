<?php

namespace App\Repositories;

/**
 * Data access for admin activity logs.
 */
class ActivityLogRepository extends BaseRepository
{
    /**
     * Store an activity log row.
     *
     * @param int|null $adminUserId Admin user ID.
     * @param string $action Action name.
     * @param string $module Module name.
     * @param string|null $description Human-readable description.
     * @return void
     */
    public function create(?int $adminUserId, string $action, string $module, ?string $description = null): void
    {
        $this->connection->insert(
            "INSERT INTO admin_activity_logs (admin_user_id, action, module, description, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $adminUserId,
                $action,
                $module,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]
        );
    }

    /**
     * Fetch recent activity rows.
     *
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function recent(int $limit = 10): array
    {
        return $this->connection->query(
            "SELECT l.action, l.module, l.description, l.created_at, u.full_name AS user_name
             FROM admin_activity_logs l
             LEFT JOIN admin_users u ON u.id = l.admin_user_id
             ORDER BY l.created_at DESC
             LIMIT " . max(1, min(50, $limit))
        );
    }
}
