<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Services\ActivityLogService;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin role and role-permission management.
 */
class RoleController extends BaseController
{
    private RoleService $roles;
    private PermissionService $permissions;

    public function __construct()
    {
        $connection = DatabaseFactory::make();
        $activity = new ActivityLogService(new ActivityLogRepository($connection));
        $this->roles = new RoleService(new RoleRepository($connection), $activity);
        $this->permissions = new PermissionService(new PermissionRepository($connection), $activity);
    }

    public function index(): string
    {
        return $this->view('admin/roles/index', [
            'title' => 'Roles',
            'user' => $this->user(),
            'roles' => $this->roles->all(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Roles']],
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Role');
    }

    public function store(): void
    {
        $this->persist();
    }

    public function edit(string $id): string
    {
        $role = $this->roles->find((int) $id);
        if ($role === null) {
            $this->abort(404, 'Role not found.');
        }

        return $this->form('Edit Role', $role);
    }

    public function update(string $id): void
    {
        $this->persist((int) $id);
    }

    public function destroy(string $id): void
    {
        try {
            $this->roles->delete((int) $id, (int) ($this->user()['id'] ?? 0));
            $this->flash('success', 'Role deleted.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/roles');
    }

    private function form(string $title, ?array $role = null): string
    {
        return $this->view('admin/roles/form', [
            'title' => $title,
            'user' => $this->user(),
            'role' => $role,
            'permissions' => $this->permissions->grouped(),
            'selectedPermissions' => $role ? $this->roles->permissionIds((int) $role['id']) : [],
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Roles', 'url' => '/admin/roles'], ['label' => $title]],
        ]);
    }

    private function persist(?int $id = null): void
    {
        try {
            $id === null
                ? $this->roles->create($_POST, (int) ($this->user()['id'] ?? 0))
                : $this->roles->update($id, $_POST, (int) ($this->user()['id'] ?? 0));

            $this->flash('success', 'Role saved.');
            $this->redirect('/admin/roles');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/roles/create' : '/admin/roles/' . $id . '/edit');
        }
    }
}
