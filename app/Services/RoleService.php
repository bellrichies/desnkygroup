<?php

namespace App\Services;

use App\Repositories\RoleRepository;

/**
 * Business logic for role management and super-admin safeguards.
 */
class RoleService extends BaseService
{
    public function __construct(
        private RoleRepository $roles,
        private ?ActivityLogService $activityLogService = null
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->roles->all();
    }

    public function find(int $id): ?array
    {
        return $this->roles->find($id);
    }

    /**
     * @return array<int, int>
     */
    public function permissionIds(int $roleId): array
    {
        return $this->roles->permissionIds($roleId);
    }

    public function create(array $data, int $actorId): int
    {
        $roleId = $this->roles->create($this->prepare($data));
        $this->roles->syncPermissions($roleId, $this->permissionIdsFromPayload($data));
        $this->log($actorId, 'created', 'roles', 'Created role ' . (string) $data['name']);

        return $roleId;
    }

    public function update(int $id, array $data, int $actorId): bool
    {
        $role = $this->roles->find($id);
        if ($role === null) {
            throw new \RuntimeException('Role not found.');
        }

        if (($role['slug'] ?? '') === 'super-admin') {
            throw new \RuntimeException('The Super Admin role cannot be modified.');
        }

        $this->roles->update($id, $this->prepare($data));
        $this->roles->syncPermissions($id, $this->permissionIdsFromPayload($data));
        $this->log($actorId, 'updated', 'roles', 'Updated role ' . (string) $data['name']);

        return true;
    }

    public function delete(int $id, int $actorId): bool
    {
        $role = $this->roles->find($id);
        if ($role === null) {
            throw new \RuntimeException('Role not found.');
        }

        if (!empty($role['is_system_role'])) {
            throw new \RuntimeException('System roles cannot be deleted.');
        }

        $this->roles->delete($id);
        $this->log($actorId, 'deleted', 'roles', 'Deleted role ' . (string) $role['name']);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new \InvalidArgumentException('Role name is required.');
        }

        return [
            'name' => $name,
            'slug' => $this->slug((string) ($data['slug'] ?? $name)),
            'description' => trim((string) ($data['description'] ?? '')),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_system_role' => false,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function permissionIdsFromPayload(array $data): array
    {
        return array_map('intval', (array) ($data['permission_ids'] ?? []));
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));

        return $slug !== '' ? $slug : 'role-' . time();
    }

    private function log(int $actorId, string $action, string $module, string $description): void
    {
        $this->activityLogService?->record($actorId, $action, $module, $description);
    }
}
