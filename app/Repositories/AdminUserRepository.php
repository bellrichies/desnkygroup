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
            "UPDATE admin_users SET last_login_at = NOW() WHERE id = ?",
            [$id]
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
}
