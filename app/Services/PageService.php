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
}
