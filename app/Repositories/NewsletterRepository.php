<?php

namespace App\Repositories;

/**
 * Data access for newsletter subscribers.
 */
class NewsletterRepository extends BaseRepository
{
    public function findByEmail(string $email): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM newsletter_subscribers WHERE email = ? LIMIT 1',
            [$email]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function all(array $filters = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $where[] = '(email LIKE ? OR name LIKE ?)';
            $params[] = $term;
            $params[] = $term;
        }

        $sql = 'SELECT * FROM newsletter_subscribers WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';

        return $this->connection->query($sql, $params);
    }

    public function findById(int $id): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM newsletter_subscribers WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public function unsubscribe(int $id): bool
    {
        $this->connection->update(
            "UPDATE newsletter_subscribers SET status = 'unsubscribed', unsubscribed_at = NOW() WHERE id = ?",
            [$id]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM newsletter_subscribers WHERE id = ?", [$id]);

        return true;
    }

    public function subscribe(string $email, ?string $name = null, string $source = 'homepage'): int
    {
        $existing = $this->findByEmail($email);

        if ($existing !== null) {
            $this->connection->update(
                "UPDATE newsletter_subscribers
                 SET name = COALESCE(?, name), status = 'subscribed', source = ?, unsubscribed_at = NULL
                 WHERE email = ?",
                [$name, $source, $email]
            );

            return (int) $existing['id'];
        }

        return $this->connection->insert(
            'INSERT INTO newsletter_subscribers (email, name, source) VALUES (?, ?, ?)',
            [$email, $name, $source]
        );
    }
}
