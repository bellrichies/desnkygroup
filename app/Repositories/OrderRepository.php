<?php

namespace App\Repositories;

/**
 * Data access for orders and order items.
 */
class OrderRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function filter(array $filters = []): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'order_status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(order_number LIKE ? OR customer_email LIKE ? OR customer_name LIKE ?)';
            $search = '%' . $filters['q'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['from'])) {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $filters['from'];
        }

        if (!empty($filters['to'])) {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $filters['to'];
        }

        return $this->connection->query(
            "SELECT * FROM orders
             WHERE " . implode(' AND ', $where) . "
             ORDER BY created_at DESC",
            $params
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM orders WHERE id = ?", [$id]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(int $orderId): array
    {
        return $this->connection->query(
            "SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC",
            [$orderId]
        );
    }

    /**
     * Persist an order and its items atomically.
     *
     * @param array $order Order columns.
     * @param array<int, array> $items Order item rows.
     * @return int Order ID.
     */
    public function createWithItems(array $order, array $items): int
    {
        $this->connection->beginTransaction();

        try {
            $orderId = $this->connection->insert(
                "INSERT INTO orders (
                    order_number, customer_email, customer_name, customer_phone,
                    shipping_address, shipping_city, shipping_state, shipping_postal_code,
                    subtotal, shipping_cost, tax, total, payment_method, payment_status,
                    order_status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $order['order_number'],
                    $order['customer_email'],
                    $order['customer_name'],
                    $order['customer_phone'],
                    $order['shipping_address'],
                    $order['shipping_city'],
                    $order['shipping_state'],
                    $order['shipping_postal_code'] ?? '',
                    $order['subtotal'],
                    $order['shipping_cost'],
                    $order['tax'] ?? 0,
                    $order['total'],
                    $order['payment_method'],
                    $order['payment_status'] ?? 'pending',
                    $order['order_status'] ?? 'pending',
                    $order['notes'] ?? null,
                ]
            );

            foreach ($items as $item) {
                $this->connection->insert(
                    "INSERT INTO order_items (
                        order_id, product_id, product_name, unit_price, quantity, line_total
                    ) VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $orderId,
                        $item['product_id'] ?? null,
                        $item['product_name'],
                        $item['unit_price'],
                        $item['quantity'],
                        $item['line_total'],
                    ]
                );
            }

            $this->connection->commit();

            return $orderId;
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $columns = ['order_status = ?'];
        $params = [$status];

        if ($status === 'shipped') {
            $columns[] = 'shipped_at = COALESCE(shipped_at, NOW())';
        }

        if ($status === 'delivered') {
            $columns[] = 'delivered_at = COALESCE(delivered_at, NOW())';
            $columns[] = "payment_status = CASE WHEN payment_status = 'pending' THEN 'completed' ELSE payment_status END";
        }

        $params[] = $id;

        $this->connection->update(
            "UPDATE orders SET " . implode(', ', $columns) . " WHERE id = ?",
            $params
        );

        return true;
    }

    public function markRefunded(int $id, string $notes): bool
    {
        $this->connection->update(
            "UPDATE orders
             SET payment_status = 'refunded',
                 order_status = 'cancelled',
                 notes = CONCAT(COALESCE(notes, ''), ?)
             WHERE id = ?",
            ["\nRefund note: " . $notes, $id]
        );

        return true;
    }
}
