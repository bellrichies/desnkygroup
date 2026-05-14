<?php

namespace App\Repositories;

/**
 * Data access for orders and order items.
 */
class OrderRepository extends BaseRepository
{
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
}
