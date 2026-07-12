<?php

namespace App\Repositories;

class FaqRepository extends BaseRepository
{
    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->connection->query(
            "SELECT * FROM faqs ORDER BY sort_order ASC, created_at ASC"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function active(): array
    {
        return $this->connection->query(
            "SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC"
        );
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM faqs WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES (?, ?, ?, ?, ?)",
            [
                $data['question'],
                $data['answer'],
                $data['category'] ?? 'General',
                (int) ($data['sort_order'] ?? 0),
                isset($data['is_active']) ? 1 : 0,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, is_active = ? WHERE id = ?",
            [
                $data['question'],
                $data['answer'],
                $data['category'] ?? 'General',
                (int) ($data['sort_order'] ?? 0),
                isset($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM faqs WHERE id = ?", [$id]);

        return true;
    }

    public function toggleActive(int $id): bool
    {
        $this->connection->update(
            "UPDATE faqs SET is_active = 1 - is_active WHERE id = ?",
            [$id]
        );

        return true;
    }
}
