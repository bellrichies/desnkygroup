<?php

namespace App\Services;

use App\Repositories\ServiceRepository;

/**
 * Business logic for admin-managed service content.
 */
class ServiceService extends BaseService
{
    public function __construct(private ServiceRepository $services)
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
        return $this->services->published();
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

        return $this->services->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        return $this->services->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->services->delete($id);
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'service-' . time();
    }
}
