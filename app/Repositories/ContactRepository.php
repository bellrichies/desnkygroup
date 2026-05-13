<?php

namespace App\Repositories;

/**
 * ContactRepository - Data access layer for contacts
 */
class ContactRepository extends BaseRepository
{
    /**
     * Get all contacts
     *
     * @return array
     */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM contacts ORDER BY created_at DESC"
        );
    }

    /**
     * Get contacts by status
     *
     * @param string $status Contact status
     * @return array
     */
    public function byStatus(string $status): array
    {
        return $this->connection->query(
            "SELECT * FROM contacts WHERE status = ? ORDER BY created_at DESC",
            [$status]
        );
    }

    /**
     * Find contact by ID
     *
     * @param int $id Contact ID
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM contacts WHERE id = ?",
            [$id]
        );

        return $result ? array_shift($result) : null;
    }

    /**
     * Find contacts by email
     *
     * @param string $email Email address
     * @return array
     */
    public function byEmail(string $email): array
    {
        return $this->connection->query(
            "SELECT * FROM contacts WHERE email = ? ORDER BY created_at DESC",
            [$email]
        );
    }

    /**
     * Search contacts
     *
     * @param string $query Search query
     * @return array
     */
    public function search(string $query): array
    {
        $searchTerm = "%{$query}%";

        return $this->connection->query(
            "SELECT * FROM contacts 
             WHERE full_name LIKE ? OR email LIKE ? OR subject LIKE ?
             ORDER BY created_at DESC",
            [$searchTerm, $searchTerm, $searchTerm]
        );
    }

    /**
     * Create a new contact submission
     *
     * @param array $data Contact data
     * @return int Contact ID
     */
    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO contacts (full_name, email, phone, company, subject, message, source) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['full_name'] ?? '',
                $data['email'] ?? '',
                $data['phone'] ?? '',
                $data['company'] ?? null,
                $data['subject'] ?? '',
                $data['message'] ?? '',
                $data['source'] ?? 'contact_form',
            ]
        );
    }

    /**
     * Update a contact
     *
     * @param int $id Contact ID
     * @param array $data Updated data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE contacts SET status = ?, internal_notes = ?, assigned_to = ? WHERE id = ?",
            [
                $data['status'] ?? 'new',
                $data['internal_notes'] ?? null,
                $data['assigned_to'] ?? null,
                $id,
            ]
        );

        return true;
    }

    /**
     * Mark contact as read
     *
     * @param int $id Contact ID
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        $this->connection->update(
            "UPDATE contacts SET status = 'read' WHERE id = ? AND status = 'new'",
            [$id]
        );

        return true;
    }

    /**
     * Mark contact as responded
     *
     * @param int $id Contact ID
     * @return bool
     */
    public function markAsResponded(int $id): bool
    {
        $this->connection->update(
            "UPDATE contacts SET status = 'responded', responded_at = NOW() WHERE id = ?",
            [$id]
        );

        return true;
    }

    /**
     * Assign contact to admin user
     *
     * @param int $id Contact ID
     * @param int $adminId Admin user ID
     * @return bool
     */
    public function assign(int $id, int $adminId): bool
    {
        $this->connection->update(
            "UPDATE contacts SET assigned_to = ? WHERE id = ?",
            [$adminId, $id]
        );

        return true;
    }

    /**
     * Delete a contact
     *
     * @param int $id Contact ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->connection->delete(
            "DELETE FROM contacts WHERE id = ?",
            [$id]
        );

        return true;
    }
}
