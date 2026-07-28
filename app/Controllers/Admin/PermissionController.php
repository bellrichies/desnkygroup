<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\PermissionRepository;
use App\Services\ActivityLogService;
use App\Services\PermissionService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Permission catalog management.
 */
class PermissionController extends BaseController
{
    private PermissionService $permissions;

    public function __construct()
    {
        $connection = DatabaseFactory::make();
        $this->permissions = new PermissionService(
            new PermissionRepository($connection),
            new ActivityLogService(new ActivityLogRepository($connection))
        );
    }

    public function index(): string
    {
        return $this->view('admin/permissions/index', [
            'title' => 'Permissions',
            'user' => $this->user(),
            'groupedPermissions' => $this->permissions->grouped(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Permissions']],
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Permission');
    }

    public function store(): void
    {
        $this->persist();
    }

    public function edit(string $id): string
    {
        $permission = $this->permissions->find((int) $id);
        if ($permission === null) {
            $this->abort(404, 'Permission not found.');
        }

        return $this->form('Edit Permission', $permission);
    }

    public function update(string $id): void
    {
        $this->persist((int) $id);
    }

    private function form(string $title, ?array $permission = null): string
    {
        return $this->view('admin/permissions/form', [
            'title' => $title,
            'user' => $this->user(),
            'permission' => $permission,
            'permissions' => $this->permissions->all(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Permissions', 'url' => '/admin/permissions'], ['label' => $title]],
        ]);
    }

    private function persist(?int $id = null): void
    {
        try {
            $id === null
                ? $this->permissions->create($_POST, (int) ($this->user()['id'] ?? 0))
                : $this->permissions->update($id, $_POST, (int) ($this->user()['id'] ?? 0));

            $this->flash('success', 'Permission saved.');
            $this->redirect('/admin/permissions');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/permissions/create' : '/admin/permissions/' . $id . '/edit');
        }
    }
}
