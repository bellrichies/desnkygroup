<?php

namespace App\Repositories;

/**
 * Data access for blog categories, tags, and author profiles.
 */
class BlogTaxonomyRepository extends BaseRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(bool $includeInactive = true): array
    {
        $where = ['c.deleted_at IS NULL'];
        if (!$includeInactive) {
            $where[] = 'c.is_active = 1';
        }

        return $this->connection->query(
            "SELECT c.*,
                    (
                        SELECT COUNT(*)
                        FROM blog_post_categories pc
                        INNER JOIN blog_posts p ON p.id = pc.post_id
                        WHERE pc.category_id = c.id
                        AND p.deleted_at IS NULL
                        AND " . $this->publishedCondition('p') . "
                    ) AS post_count
             FROM blog_categories c
             WHERE " . implode(' AND ', $where) . "
             ORDER BY c.sort_order ASC, c.name ASC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function tags(): array
    {
        return $this->connection->query(
            "SELECT t.*,
                    (
                        SELECT COUNT(*)
                        FROM blog_post_tags pt
                        INNER JOIN blog_posts p ON p.id = pt.post_id
                        WHERE pt.tag_id = t.id
                        AND p.deleted_at IS NULL
                        AND " . $this->publishedCondition('p') . "
                    ) AS post_count
             FROM blog_tags t
             WHERE t.deleted_at IS NULL
             ORDER BY t.name ASC"
        );
    }

    public function findCategory(int $id): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM blog_categories WHERE id = ? AND deleted_at IS NULL',
            [$id]
        );
    }

    public function findCategoryBySlug(string $slug): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM blog_categories WHERE slug = ? AND is_active = 1 AND deleted_at IS NULL',
            [$slug]
        );
    }

    public function findTag(int $id): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM blog_tags WHERE id = ? AND deleted_at IS NULL',
            [$id]
        );
    }

    public function findTagBySlug(string $slug): ?array
    {
        return $this->connection->queryOne(
            'SELECT * FROM blog_tags WHERE slug = ? AND deleted_at IS NULL',
            [$slug]
        );
    }

    public function categorySlugExists(string $slug, ?int $excludeId = null): bool
    {
        $params = [$slug];
        $sql = 'SELECT id FROM blog_categories WHERE slug = ? AND deleted_at IS NULL';
        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }

        return $this->connection->queryOne($sql, $params) !== null;
    }

    public function tagSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $params = [$slug];
        $sql = 'SELECT id FROM blog_tags WHERE slug = ? AND deleted_at IS NULL';
        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }

        return $this->connection->queryOne($sql, $params) !== null;
    }

    public function authorSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $params = [$slug];
        $sql = 'SELECT id FROM blog_authors WHERE slug = ?';
        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }

        return $this->connection->queryOne($sql, $params) !== null;
    }

    public function createCategory(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO blog_categories (
                name, slug, description, seo_title, meta_description, is_active, sort_order
             ) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['seo_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                (int) ($data['sort_order'] ?? 0),
            ]
        );
    }

    public function updateCategory(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE blog_categories
             SET name = ?, slug = ?, description = ?, seo_title = ?, meta_description = ?,
                 is_active = ?, sort_order = ?
             WHERE id = ?",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['seo_title'] ?? null,
                $data['meta_description'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                (int) ($data['sort_order'] ?? 0),
                $id,
            ]
        );

        return true;
    }

    public function deleteCategory(int $id): bool
    {
        $this->connection->update('UPDATE blog_categories SET deleted_at = NOW() WHERE id = ?', [$id]);

        return true;
    }

    public function createTag(array $data): int
    {
        return $this->connection->insert(
            'INSERT INTO blog_tags (name, slug, description) VALUES (?, ?, ?)',
            [$data['name'], $data['slug'], $data['description'] ?? null]
        );
    }

    public function updateTag(int $id, array $data): bool
    {
        $this->connection->update(
            'UPDATE blog_tags SET name = ?, slug = ?, description = ? WHERE id = ?',
            [$data['name'], $data['slug'], $data['description'] ?? null, $id]
        );

        return true;
    }

    public function deleteTag(int $id): bool
    {
        $this->connection->update('UPDATE blog_tags SET deleted_at = NOW() WHERE id = ?', [$id]);

        return true;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function authors(bool $activeOnly = false): array
    {
        $where = $activeOnly ? 'WHERE a.is_active = 1' : '';

        return $this->connection->query(
            "SELECT a.*,
                    (
                        SELECT COUNT(*)
                        FROM blog_posts p
                        WHERE p.author_id = a.id AND p.deleted_at IS NULL
                    ) AS post_count
             FROM blog_authors a
             {$where}
             ORDER BY a.display_name ASC"
        );
    }

    public function findAuthor(int $id): ?array
    {
        return $this->connection->queryOne('SELECT * FROM blog_authors WHERE id = ?', [$id]);
    }

    public function findAuthorByAdminUserId(int $adminUserId): ?array
    {
        return $this->connection->queryOne('SELECT * FROM blog_authors WHERE admin_user_id = ?', [$adminUserId]);
    }

    public function createAuthor(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO blog_authors (
                admin_user_id, display_name, slug, title, bio, avatar, email, linkedin_url, x_url, is_active
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['admin_user_id'] ?? null,
                $data['display_name'],
                $data['slug'],
                $data['title'] ?? null,
                $data['bio'] ?? null,
                $data['avatar'] ?? null,
                $data['email'] ?? null,
                $data['linkedin_url'] ?? null,
                $data['x_url'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
            ]
        );
    }

    public function updateAuthor(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE blog_authors
             SET admin_user_id = ?, display_name = ?, slug = ?, title = ?, bio = ?, avatar = ?,
                 email = ?, linkedin_url = ?, x_url = ?, is_active = ?
             WHERE id = ?",
            [
                $data['admin_user_id'] ?? null,
                $data['display_name'],
                $data['slug'],
                $data['title'] ?? null,
                $data['bio'] ?? null,
                $data['avatar'] ?? null,
                $data['email'] ?? null,
                $data['linkedin_url'] ?? null,
                $data['x_url'] ?? null,
                !empty($data['is_active']) ? 1 : 0,
                $id,
            ]
        );

        return true;
    }

    public function deleteAuthor(int $id): bool
    {
        $this->connection->update('UPDATE blog_authors SET is_active = 0 WHERE id = ?', [$id]);

        return true;
    }

    private function publishedCondition(string $alias): string
    {
        return "(
            ({$alias}.status = 'published' AND {$alias}.published_at IS NOT NULL AND {$alias}.published_at <= NOW())
            OR ({$alias}.status = 'scheduled' AND {$alias}.scheduled_at IS NOT NULL AND {$alias}.scheduled_at <= NOW())
        )";
    }
}
