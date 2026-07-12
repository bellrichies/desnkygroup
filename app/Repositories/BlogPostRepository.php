<?php

namespace App\Repositories;

use Throwable;

/**
 * Data access for blog posts and post relationships.
 */
class BlogPostRepository extends BaseRepository
{
    /**
     * @return array{items: array<int, array<string, mixed>>, total: int, page: int, per_page: int}
     */
    public function paginatePublished(array $filters = [], int $page = 1, int $perPage = 9): array
    {
        [$where, $params, $joins] = $this->publicWhere($filters);
        $page = max(1, $page);
        $perPage = max(1, min(24, $perPage));
        $offset = ($page - 1) * $perPage;

        $count = $this->connection->queryOne(
            "SELECT COUNT(DISTINCT p.id) AS aggregate
             FROM blog_posts p
             {$joins}
             WHERE " . implode(' AND ', $where),
            $params
        );

        $items = $this->connection->query(
            $this->selectSql() . "
             {$joins}
             WHERE " . implode(' AND ', $where) . "
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC, p.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => (int) ($count['aggregate'] ?? 0),
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function featured(int $limit = 3): array
    {
        $limit = max(1, min(6, $limit));

        return $this->connection->query(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND p.is_featured = 1
             AND " . $this->publishedCondition('p') . "
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC, p.id DESC
             LIMIT {$limit}"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recent(int $limit = 5): array
    {
        $limit = max(1, min(12, $limit));

        return $this->connection->query(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND " . $this->publishedCondition('p') . "
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC, p.id DESC
             LIMIT {$limit}"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function popular(int $limit = 5): array
    {
        $limit = max(1, min(12, $limit));

        return $this->connection->query(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND " . $this->publishedCondition('p') . "
             ORDER BY p.view_count DESC, COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC
             LIMIT {$limit}"
        );
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $row = $this->connection->queryOne(
            $this->selectSql() . "
             WHERE p.slug = ?
             AND p.deleted_at IS NULL
             AND " . $this->publishedCondition('p') . "
             LIMIT 1",
            [$slug]
        );

        return $row ?: null;
    }

    public function findRedirectBySlug(string $slug): ?array
    {
        return $this->connection->queryOne(
            "SELECT r.old_slug, p.slug
             FROM blog_slug_redirects r
             INNER JOIN blog_posts p ON p.id = r.post_id
             WHERE r.old_slug = ? AND p.deleted_at IS NULL
             LIMIT 1",
            [$slug]
        );
    }

    public function findAdmin(int $id, bool $withDeleted = false): ?array
    {
        $where = $withDeleted ? 'p.id = ?' : 'p.id = ? AND p.deleted_at IS NULL';
        $row = $this->connection->queryOne(
            $this->selectSql() . "
             WHERE {$where}
             LIMIT 1",
            [$id]
        );

        return $row ?: null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function related(int $postId, array $categoryIds, array $tagIds, int $limit = 3): array
    {
        $limit = max(1, min(6, $limit));
        $conditions = ['p.id <> ?', 'p.deleted_at IS NULL', $this->publishedCondition('p')];
        $params = [$postId];

        $relationFilters = [];
        if ($categoryIds !== []) {
            $relationFilters[] = 'EXISTS (
                SELECT 1 FROM blog_post_categories rpc
                WHERE rpc.post_id = p.id AND rpc.category_id IN (' . $this->placeholders($categoryIds) . ')
            )';
            $params = array_merge($params, array_values($categoryIds));
        }

        if ($tagIds !== []) {
            $relationFilters[] = 'EXISTS (
                SELECT 1 FROM blog_post_tags rpt
                WHERE rpt.post_id = p.id AND rpt.tag_id IN (' . $this->placeholders($tagIds) . ')
            )';
            $params = array_merge($params, array_values($tagIds));
        }

        if ($relationFilters !== []) {
            $conditions[] = '(' . implode(' OR ', $relationFilters) . ')';
        }

        return $this->connection->query(
            $this->selectSql() . "
             WHERE " . implode(' AND ', $conditions) . "
             ORDER BY p.is_featured DESC, COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC
             LIMIT {$limit}",
            $params
        );
    }

    /**
     * @return array{previous: array<string, mixed>|null, next: array<string, mixed>|null}
     */
    public function adjacent(array $post): array
    {
        $date = (string) ($post['published_at'] ?: $post['scheduled_at'] ?: $post['created_at']);
        $id = (int) $post['id'];

        $previous = $this->connection->queryOne(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND p.id <> ?
             AND " . $this->publishedCondition('p') . "
             AND (
                COALESCE(p.published_at, p.scheduled_at, p.created_at) < ?
                OR (COALESCE(p.published_at, p.scheduled_at, p.created_at) = ? AND p.id < ?)
             )
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC, p.id DESC
             LIMIT 1",
            [$id, $date, $date, $id]
        );

        $next = $this->connection->queryOne(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND p.id <> ?
             AND " . $this->publishedCondition('p') . "
             AND (
                COALESCE(p.published_at, p.scheduled_at, p.created_at) > ?
                OR (COALESCE(p.published_at, p.scheduled_at, p.created_at) = ? AND p.id > ?)
             )
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) ASC, p.id ASC
             LIMIT 1",
            [$id, $date, $date, $id]
        );

        return ['previous' => $previous ?: null, 'next' => $next ?: null];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminAll(array $filters = []): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (empty($filters['include_deleted'])) {
            $where[] = 'p.deleted_at IS NULL';
        }

        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM blog_post_categories pc
                WHERE pc.post_id = p.id AND pc.category_id = ?
            )';
            $params[] = (int) $filters['category_id'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)';
            $search = '%' . $filters['q'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        return $this->connection->query(
            $this->selectSql() . "
             WHERE " . implode(' AND ', $where) . "
             ORDER BY p.created_at DESC, p.id DESC",
            $params
        );
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM blog_posts WHERE slug = ?';
        $params = [$slug];

        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }

        return $this->connection->queryOne($sql, $params) !== null;
    }

    public function create(array $data): int
    {
        return $this->connection->insert(
            "INSERT INTO blog_posts (
                author_id, title, slug, excerpt, content, featured_image, featured_image_alt,
                status, is_featured, published_at, scheduled_at, seo_title, meta_description,
                canonical_url, og_image, robots_index, created_by, updated_by
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['author_id'] ?? null,
                $data['title'],
                $data['slug'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['featured_image'] ?? null,
                $data['featured_image_alt'] ?? null,
                $data['status'],
                !empty($data['is_featured']) ? 1 : 0,
                $data['published_at'] ?? null,
                $data['scheduled_at'] ?? null,
                $data['seo_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['canonical_url'] ?? null,
                $data['og_image'] ?? null,
                !empty($data['robots_index']) ? 1 : 0,
                $data['created_by'] ?? null,
                $data['updated_by'] ?? null,
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->connection->update(
            "UPDATE blog_posts
             SET author_id = ?, title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?,
                 featured_image_alt = ?, status = ?, is_featured = ?, published_at = ?,
                 scheduled_at = ?, seo_title = ?, meta_description = ?, canonical_url = ?,
                 og_image = ?, robots_index = ?, updated_by = ?
             WHERE id = ?",
            [
                $data['author_id'] ?? null,
                $data['title'],
                $data['slug'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['featured_image'] ?? null,
                $data['featured_image_alt'] ?? null,
                $data['status'],
                !empty($data['is_featured']) ? 1 : 0,
                $data['published_at'] ?? null,
                $data['scheduled_at'] ?? null,
                $data['seo_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['canonical_url'] ?? null,
                $data['og_image'] ?? null,
                !empty($data['robots_index']) ? 1 : 0,
                $data['updated_by'] ?? null,
                $id,
            ]
        );

        return true;
    }

    public function softDelete(int $id): bool
    {
        $this->connection->update('UPDATE blog_posts SET deleted_at = NOW() WHERE id = ?', [$id]);

        return true;
    }

    public function restore(int $id): bool
    {
        $this->connection->update('UPDATE blog_posts SET deleted_at = NULL WHERE id = ?', [$id]);

        return true;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $publishedAt = $status === 'published' ? ', published_at = COALESCE(published_at, NOW())' : '';
        $this->connection->update(
            "UPDATE blog_posts SET status = ? {$publishedAt} WHERE id = ? AND deleted_at IS NULL",
            [$status, $id]
        );

        return true;
    }

    public function addSlugRedirect(int $postId, string $oldSlug): void
    {
        $this->connection->insert(
            "INSERT INTO blog_slug_redirects (post_id, old_slug)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE post_id = VALUES(post_id)",
            [$postId, $oldSlug]
        );
    }

    /**
     * @param array<int, int> $categoryIds
     */
    public function syncCategories(int $postId, array $categoryIds, ?int $primaryCategoryId = null): void
    {
        $this->connection->delete('DELETE FROM blog_post_categories WHERE post_id = ?', [$postId]);

        foreach (array_values(array_unique(array_filter($categoryIds))) as $categoryId) {
            $this->connection->insert(
                'INSERT INTO blog_post_categories (post_id, category_id, is_primary) VALUES (?, ?, ?)',
                [$postId, (int) $categoryId, $primaryCategoryId === (int) $categoryId ? 1 : 0]
            );
        }
    }

    /**
     * @param array<int, int> $tagIds
     */
    public function syncTags(int $postId, array $tagIds): void
    {
        $this->connection->delete('DELETE FROM blog_post_tags WHERE post_id = ?', [$postId]);

        foreach (array_values(array_unique(array_filter($tagIds))) as $tagId) {
            $this->connection->insert(
                'INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)',
                [$postId, (int) $tagId]
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function categoriesForPost(int $postId): array
    {
        return $this->connection->query(
            "SELECT c.*, pc.is_primary
             FROM blog_categories c
             INNER JOIN blog_post_categories pc ON pc.category_id = c.id
             WHERE pc.post_id = ? AND c.deleted_at IS NULL
             ORDER BY pc.is_primary DESC, c.name ASC",
            [$postId]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function tagsForPost(int $postId): array
    {
        return $this->connection->query(
            "SELECT t.*
             FROM blog_tags t
             INNER JOIN blog_post_tags pt ON pt.tag_id = t.id
             WHERE pt.post_id = ? AND t.deleted_at IS NULL
             ORDER BY t.name ASC",
            [$postId]
        );
    }

    public function recordRevision(int $postId, ?int $adminUserId, array $data): void
    {
        $this->connection->insert(
            'INSERT INTO blog_revisions (post_id, admin_user_id, title, excerpt, content, status)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $postId,
                $adminUserId,
                $data['title'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['status'],
            ]
        );
    }

    public function incrementViewCount(int $postId): void
    {
        $this->connection->update(
            'UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?',
            [$postId]
        );
    }

    public function recordView(int $postId, ?string $ipHash, ?string $userAgentHash, ?string $referrer): void
    {
        $this->connection->insert(
            'INSERT INTO blog_post_views (post_id, ip_hash, user_agent_hash, referrer) VALUES (?, ?, ?, ?)',
            [$postId, $ipHash, $userAgentHash, $referrer]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function publishedForSitemap(): array
    {
        return $this->connection->query(
            "SELECT slug, updated_at
             FROM blog_posts p
             WHERE p.deleted_at IS NULL
             AND p.robots_index = 1
             AND " . $this->publishedCondition('p') . "
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC"
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function publishedForFeed(int $limit = 20): array
    {
        $limit = max(1, min(50, $limit));

        return $this->connection->query(
            $this->selectSql() . "
             WHERE p.deleted_at IS NULL
             AND " . $this->publishedCondition('p') . "
             ORDER BY COALESCE(p.published_at, p.scheduled_at, p.created_at) DESC
             LIMIT {$limit}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function stats(): array
    {
        $statusRows = $this->connection->query(
            "SELECT status, COUNT(*) AS aggregate
             FROM blog_posts
             WHERE deleted_at IS NULL
             GROUP BY status"
        );

        $statusCounts = [
            'total' => 0,
            'draft' => 0,
            'scheduled' => 0,
            'published' => 0,
            'archived' => 0,
        ];

        foreach ($statusRows as $row) {
            $count = (int) $row['aggregate'];
            $statusCounts[(string) $row['status']] = $count;
            $statusCounts['total'] += $count;
        }

        $views = $this->connection->queryOne(
            'SELECT COALESCE(SUM(view_count), 0) AS aggregate FROM blog_posts WHERE deleted_at IS NULL'
        );

        return [
            'counts' => $statusCounts,
            'total_views' => (int) ($views['aggregate'] ?? 0),
            'most_viewed' => $this->popular(5),
            'recent' => $this->connection->query(
                "SELECT id, title, status, updated_at
                 FROM blog_posts
                 WHERE deleted_at IS NULL
                 ORDER BY updated_at DESC
                 LIMIT 8"
            ),
        ];
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function transaction(callable $callback)
    {
        $this->connection->beginTransaction();

        try {
            $result = $callback();
            $this->connection->commit();

            return $result;
        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, mixed>, 2: string}
     */
    private function publicWhere(array $filters): array
    {
        $where = ['p.deleted_at IS NULL', $this->publishedCondition('p')];
        $params = [];
        $joins = '';

        if (!empty($filters['category_slug'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM blog_post_categories pc
                INNER JOIN blog_categories c ON c.id = pc.category_id
                WHERE pc.post_id = p.id AND c.slug = ? AND c.is_active = 1 AND c.deleted_at IS NULL
            )';
            $params[] = $filters['category_slug'];
        }

        if (!empty($filters['tag_slug'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM blog_post_tags pt
                INNER JOIN blog_tags t ON t.id = pt.tag_id
                WHERE pt.post_id = p.id AND t.slug = ? AND t.deleted_at IS NULL
            )';
            $params[] = $filters['tag_slug'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(p.title LIKE ? OR p.excerpt LIKE ? OR p.content LIKE ?)';
            $search = '%' . $filters['q'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        return [$where, $params, $joins];
    }

    private function selectSql(): string
    {
        return "SELECT
                    p.*,
                    COALESCE(p.published_at, p.scheduled_at, p.created_at) AS publish_date,
                    a.display_name AS author_name,
                    a.slug AS author_slug,
                    a.title AS author_title,
                    a.bio AS author_bio,
                    a.avatar AS author_avatar,
                    (
                        SELECT c.name
                        FROM blog_categories c
                        INNER JOIN blog_post_categories pc ON pc.category_id = c.id
                        WHERE pc.post_id = p.id AND c.deleted_at IS NULL
                        ORDER BY pc.is_primary DESC, c.name ASC
                        LIMIT 1
                    ) AS category_name,
                    (
                        SELECT c.slug
                        FROM blog_categories c
                        INNER JOIN blog_post_categories pc ON pc.category_id = c.id
                        WHERE pc.post_id = p.id AND c.deleted_at IS NULL
                        ORDER BY pc.is_primary DESC, c.name ASC
                        LIMIT 1
                    ) AS category_slug,
                    (
                        SELECT GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ')
                        FROM blog_tags t
                        INNER JOIN blog_post_tags pt ON pt.tag_id = t.id
                        WHERE pt.post_id = p.id AND t.deleted_at IS NULL
                    ) AS tag_names
                FROM blog_posts p
                LEFT JOIN blog_authors a ON a.id = p.author_id";
    }

    private function publishedCondition(string $alias): string
    {
        return "(
            ({$alias}.status = 'published' AND {$alias}.published_at IS NOT NULL AND {$alias}.published_at <= NOW())
            OR ({$alias}.status = 'scheduled' AND {$alias}.scheduled_at IS NOT NULL AND {$alias}.scheduled_at <= NOW())
        )";
    }

    /**
     * @param array<int, mixed> $values
     */
    private function placeholders(array $values): string
    {
        return implode(', ', array_fill(0, count($values), '?'));
    }
}
