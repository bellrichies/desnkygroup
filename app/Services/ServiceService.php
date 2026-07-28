<?php

namespace App\Services;

use App\Repositories\ServiceRepository;

/**
 * Business logic for admin-managed service content.
 */
class ServiceService extends BaseService
{
    public function __construct(private ServiceRepository $services, private ?CacheService $cache = null)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->services->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function published(): array
    {
        if ($this->cache === null) {
            return $this->services->published();
        }

        return $this->cache->remember(
            'services.published',
            600,
            fn (): array => $this->services->published(),
            ['services']
        );
    }

    public function getById(int $id): ?array
    {
        return $this->services->find($id);
    }

    public function getPublishedBySlug(string $slug): ?array
    {
        return $this->services->findPublishedBySlug($slug);
    }

    public function create(array $data): int
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        $id = $this->services->create($data);
        $this->cache?->flushTag('services');

        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        $updated = $this->services->update($id, $data);
        $this->cache?->flushTag('services');

        return $updated;
    }

    public function delete(int $id): bool
    {
        $deleted = $this->services->delete($id);
        $this->cache?->flushTag('services');

        return $deleted;
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'service-' . time();
    }
}
