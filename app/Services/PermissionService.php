<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

/**
 * Business logic for permission catalog management.
 */
class PermissionService extends BaseService
{
    public function __construct(
        private PermissionRepository $permissions,
        private ?ActivityLogService $activityLogService = null
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->permissions->all();
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function grouped(): array
    {
        return $this->permissions->grouped();
    }

    public function find(int $id): ?array
    {
        return $this->permissions->find($id);
    }

    public function create(array $data, int $actorId): int
    {
        $permissionId = $this->permissions->create($this->prepare($data));
        $this->log($actorId, 'created', 'permissions', 'Created permission ' . (string) $data['slug']);

        return $permissionId;
    }

    public function update(int $id, array $data, int $actorId): bool
    {
        $this->permissions->update($id, $this->prepare($data));
        $this->log($actorId, 'updated', 'permissions', 'Updated permission ' . (string) $data['slug']);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        $slug = strtolower(trim((string) ($data['slug'] ?? '')));
        $module = strtolower(trim((string) ($data['module'] ?? '')));
        $name = trim((string) ($data['name'] ?? ''));

        if ($slug === '' || $module === '' || $name === '') {
            throw new \InvalidArgumentException('Name, module and slug are required.');
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'module' => $module,
            'parent_id' => (int) ($data['parent_id'] ?? 0),
            'description' => trim((string) ($data['description'] ?? '')),
        ];
    }

    private function log(int $actorId, string $action, string $module, string $description): void
    {
        $this->activityLogService?->record($actorId, $action, $module, $description);
    }
}
