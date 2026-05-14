<?php

namespace App\Repositories;

/**
 * Aggregated dashboard data queries.
 */
class DashboardRepository extends BaseRepository
{
    /**
     * Count rows in a table using an optional condition.
     *
     * @param string $table Table name.
     * @param string $where SQL WHERE clause without the WHERE keyword.
     * @param array<int, mixed> $params Query parameters.
     * @return int
     */
    public function count(string $table, string $where = '1 = 1', array $params = []): int
    {
        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate FROM {$table} WHERE {$where}",
            $params
        );

        return (int) ($row['aggregate'] ?? 0);
    }

    /**
     * Sum one numeric column.
     *
     * @param string $table Table name.
     * @param string $column Column name.
     * @param string $where SQL WHERE clause without the WHERE keyword.
     * @param array<int, mixed> $params Query parameters.
     * @return float
     */
    public function sum(string $table, string $column, string $where = '1 = 1', array $params = []): float
    {
        $row = $this->connection->queryOne(
            "SELECT COALESCE(SUM({$column}), 0) AS aggregate FROM {$table} WHERE {$where}",
            $params
        );

        return (float) ($row['aggregate'] ?? 0);
    }

    /**
     * Fetch recent contact inquiries.
     *
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function recentInquiries(int $limit = 5): array
    {
        return $this->connection->query(
            "SELECT id, full_name, email, subject, status, created_at
             FROM contacts
             ORDER BY created_at DESC
             LIMIT " . max(1, min(20, $limit))
        );
    }

    /**
     * Fetch recent ecommerce orders.
     *
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function recentOrders(int $limit = 5): array
    {
        return $this->connection->query(
            "SELECT id, order_number, customer_name, total, order_status, created_at
             FROM orders
             ORDER BY created_at DESC
             LIMIT " . max(1, min(20, $limit))
        );
    }
}
