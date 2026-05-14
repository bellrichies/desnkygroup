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
