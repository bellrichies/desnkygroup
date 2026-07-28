<?php

namespace App\Repositories;

/**
 * Reads customer-facing records from orders, inquiries, and newsletter tables.
 */
class CustomerRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function orderRows(): array
    {
        return $this->connection->query(
            "SELECT id, order_number, customer_email AS email, customer_name AS name,
                    customer_phone AS phone, total, order_status, created_at
             FROM orders
             WHERE customer_email IS NOT NULL AND customer_email <> ''
             ORDER BY created_at DESC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inquiryRows(): array
    {
        return $this->connection->query(
            "SELECT id, full_name AS name, email, phone, company, subject, status, source, created_at
             FROM contacts
             WHERE email IS NOT NULL AND email <> ''
             ORDER BY created_at DESC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function newsletterRows(): array
    {
        return $this->connection->query(
            "SELECT id, email, name, status, source, subscribed_at, created_at, updated_at
             FROM newsletter_subscribers
             WHERE email IS NOT NULL AND email <> ''
             ORDER BY updated_at DESC"
        );
    }
}
