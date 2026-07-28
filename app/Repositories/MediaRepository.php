<?php

namespace App\Repositories;

/**
 * Data access layer for media library assets.
 */
class MediaRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(?string $search = null): array
    {
        if ($search !== null && $search !== '') {
            return $this->connection->query(
                "SELECT * FROM media_library
                 WHERE filename LIKE ? OR title LIKE ? OR alt_text LIKE ?
                 ORDER BY created_at DESC",
                ["%{$search}%", "%{$search}%", "%{$search}%"]
            );
        }

        return $this->connection->query("SELECT * FROM media_library ORDER BY created_at DESC");
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM media_library WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO media_library (disk, path, filename, mime_type, size, alt_text, title, uploaded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['disk'] ?? 'public',
                $data['path'],
                $data['filename'],
                $data['mime_type'],
                (int) $data['size'],
                $data['alt_text'] ?? null,
                $data['title'] ?? null,
                $data['uploaded_by'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE media_library SET alt_text = ?, title = ? WHERE id = ?",
            [$data['alt_text'] ?? null, $data['title'] ?? null, $id]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM media_library WHERE id = ?", [$id]);

        return true;
    }
}
