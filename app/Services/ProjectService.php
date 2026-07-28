<?php

namespace App\Services;

use App\Repositories\ProjectRepository;

/**
 * Public project/gallery business logic.
 */
class ProjectService extends BaseService
{
    public function __construct(private ProjectRepository $projects, private ?CacheService $cache = null)
    {
    }

    /**
     * @return array<int, array>
     */
    public function published(): array
    {
        $projects = $this->cache === null
            ? $this->projects->published()
            : $this->cache->remember(
                'projects.published',
                600,
                fn (): array => $this->projects->published(),
                ['projects']
            );

        return array_map(function (array $project): array {
            $project['title'] = (string) ($project['title'] ?? '');
            $project['summary'] = (string) ($project['summary'] ?? '');
            $project['category'] = (string) ($project['category'] ?? 'Project');
            $project['image'] = $project['featured_image']
                ?: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=900&q=80';

            return $project;
        }, $projects);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->projects->all();
    }

    public function getById(int $id): ?array
    {
        return $this->projects->find($id);
    }

    public function getPublishedBySlug(string $slug): ?array
    {
        return $this->projects->findPublishedBySlug($slug);
    }

    public function create(array $data): int
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        $id = $this->projects->create($data);
        $this->cache?->flushTag('projects');

        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $data['slug'] = $this->slug($data['slug'] ?? $data['title'] ?? '');

        $updated = $this->projects->update($id, $data);
        $this->cache?->flushTag('projects');

        return $updated;
    }

    public function delete(int $id): bool
    {
        $deleted = $this->projects->delete($id);
        $this->cache?->flushTag('projects');

        return $deleted;
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $value), '-'));

        return $slug !== '' ? $slug : 'project-' . time();
    }
}
