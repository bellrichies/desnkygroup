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

    public function paginated(
        int $page = 1,
        int $perPage = 20,
        ?string $search = null,
        ?string $mediaType = null
    ): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(20, $perPage));
        $offset = ($page - 1) * $perPage;
        $params = [];
        $conditions = [];

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $conditions[] = '(filename LIKE ? OR title LIKE ? OR alt_text LIKE ?)';
            $params = [$term, $term, $term];
        }
        if (in_array($mediaType, ['image', 'video', 'document'], true)) {
            $conditions[] = 'media_type = ?';
            $params[] = $mediaType;
        }
        $where = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $items = $this->connection->query(
            "SELECT * FROM media_library {$where}
             ORDER BY created_at DESC, id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $count = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate FROM media_library {$where}",
            $params
        );

        return [
            'items' => $items,
            'total' => (int) ($count['aggregate'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function typeCounts(?string $search = null): array
    {
        $params = [];
        $where = '';
        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $where = 'WHERE filename LIKE ? OR title LIKE ? OR alt_text LIKE ?';
            $params = [$term, $term, $term];
        }

        $rows = $this->connection->query(
            "SELECT media_type, COUNT(*) AS aggregate
             FROM media_library {$where}
             GROUP BY media_type",
            $params
        );
        $counts = ['all' => 0, 'image' => 0, 'video' => 0, 'document' => 0];
        foreach ($rows as $row) {
            $type = (string) ($row['media_type'] ?? '');
            if (isset($counts[$type])) {
                $counts[$type] = (int) $row['aggregate'];
                $counts['all'] += (int) $row['aggregate'];
            }
        }

        return $counts;
    }

    public function find(int $id): ?array
    {
        return $this->connection->queryOne("SELECT * FROM media_library WHERE id = ?", [$id]);
    }

    public function findByPath(string $path): ?array
    {
        return $this->connection->queryOne("SELECT * FROM media_library WHERE path = ?", [$path]);
    }

    public function findImageByPath(string $path): ?array
    {
        return $this->connection->queryOne(
            "SELECT * FROM media_library WHERE path = ? AND media_type = 'image' LIMIT 1",
            [$path]
        );
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO media_library
                (disk, path, filename, mime_type, media_type, size, width, height,
                 alt_text, seo_description, caption, tags, title, uploaded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['disk'] ?? 'public',
                $data['path'],
                $data['filename'],
                $data['mime_type'],
                $data['media_type'] ?? 'image',
                (int) $data['size'],
                $data['width'] ?? null,
                $data['height'] ?? null,
                $data['alt_text'] ?? null,
                $data['seo_description'] ?? null,
                $data['caption'] ?? null,
                $data['tags'] ?? null,
                $data['title'] ?? null,
                $data['uploaded_by'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE media_library
             SET alt_text = ?, seo_description = ?, caption = ?, tags = ?, title = ?
             WHERE id = ?",
            [
                $data['alt_text'] ?? null,
                $data['seo_description'] ?? null,
                $data['caption'] ?? null,
                $data['tags'] ?? null,
                $data['title'] ?? null,
                $id,
            ]
        );

        return true;
    }

    public function updateFile(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE media_library
             SET mime_type = ?, media_type = ?, size = ?, width = ?, height = ?, alt_text = ?, title = ?
             WHERE id = ?",
            [
                $data['mime_type'],
                $data['media_type'] ?? 'image',
                (int) $data['size'],
                $data['width'] ?? null,
                $data['height'] ?? null,
                $data['alt_text'] ?? null,
                $data['title'] ?? null,
                $id,
            ]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $this->connection->delete("DELETE FROM media_library WHERE id = ?", [$id]);

        return true;
    }
}
