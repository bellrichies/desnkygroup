<?php

namespace App\Services;

use App\Repositories\PageRepository;

/**
 * PageService - Business logic for CMS pages.
 */
class PageService extends BaseService
{
    private PageRepository $pages;

    public function __construct(PageRepository $pages)
    {
        $this->pages = $pages;
    }

    /**
     * Get all published pages.
     *
     * @return array
     */
    public function getPublished(): array
    {
        return $this->pages->published();
    }

    /**
     * Get all admin-visible pages.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->pages->all();
    }

    /**
     * Get a published page by slug.
     *
     * @param string $slug Page slug.
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->pages->findPublishedBySlug($slug);
    }

    /**
     * Get page by ID.
     *
     * @param int $id Page ID.
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->pages->find($id);
    }

    /**
     * Create a new page.
     *
     * @param array $data Page data.
     * @return int Page ID.
     */
    public function create(array $data): int
    {
        $data['created_by'] = $data['created_by'] ?? 1;
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        return $this->pages->create($data);
    }

    /**
     * Update a page.
     *
     * @param int $id Page ID.
     * @param array $data Updated data.
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        return $this->pages->update($id, $data);
    }

    /**
     * Delete a page.
     *
     * @param int $id Page ID.
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->pages->delete($id);
    }

    /**
     * Publish a page.
     *
     * @param int $id Page ID.
     * @return bool
     */
    public function publish(int $id): bool
    {
        return $this->pages->publish($id);
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'page-' . time();
    }
}
