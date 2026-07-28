<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\AdminUserRepository;
use App\Repositories\RoleRepository;
use App\Services\ActivityLogService;
use App\Services\AdminUserService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin user CRUD, role assignment and password reset.
 */
class UserController extends BaseController
{
    private AdminUserService $users;
    private RoleRepository $roles;

    public function __construct()
    {
        $connection = DatabaseFactory::make();
        $activity = new ActivityLogService(new ActivityLogRepository($connection));
        $this->roles = new RoleRepository($connection);
        $this->users = new AdminUserService(new AdminUserRepository($connection), $this->roles, $activity);
    }

    public function index(): string
    {
        return $this->view('admin/users/index', [
            'title' => 'Admin Users',
            'user' => $this->user(),
            'users' => $this->users->all($_GET['q'] ?? null),
            'filters' => $_GET,
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Admin Users']],
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Admin User');
    }

    public function store(): void
    {
        try {
            $result = $this->users->create($_POST, (int) ($this->user()['id'] ?? 0));
            $this->flash('success', 'Admin user created. Temporary password: ' . $result['temporary_password']);
            $this->redirect('/admin/users');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/users/create');
        }
    }

    public function edit(string $id): string
    {
        $adminUser = $this->users->find((int) $id);
        if ($adminUser === null) {
            $this->abort(404, 'Admin user not found.');
        }

        return $this->form('Edit Admin User', $adminUser);
    }

    public function update(string $id): void
    {
        try {
            $this->users->update((int) $id, $_POST, (int) ($this->user()['id'] ?? 0));
            $this->flash('success', 'Admin user updated.');
            $this->redirect('/admin/users');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/users/' . (int) $id . '/edit');
        }
    }

    public function destroy(string $id): void
    {
        try {
            $this->users->delete((int) $id, (int) ($this->user()['id'] ?? 0));
            $this->flash('success', 'Admin user deleted.');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/users');
    }

    public function resetPassword(string $id): void
    {
        try {
            $password = $this->users->resetPassword((int) $id, (int) ($this->user()['id'] ?? 0));
            $this->flash('success', 'Temporary password: ' . $password);
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/users/' . (int) $id . '/edit');
    }

    public function export(): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="admin-users-' . date('Y-m-d') . '.csv"');

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Name', 'Email', 'Roles', 'Active', 'Last Login']);

        foreach ($this->users->all($_GET['q'] ?? null) as $adminUser) {
            fputcsv($handle, [
                $adminUser['full_name'],
                $adminUser['email'],
                $adminUser['role_names'],
                !empty($adminUser['is_active']) ? 'yes' : 'no',
                $adminUser['last_login_at'],
            ]);
        }

        fclose($handle);
        exit;
    }

    private function form(string $title, ?array $adminUser = null): string
    {
        $selectedRoles = $adminUser ? $this->users->roleIds((int) $adminUser['id']) : [];

        return $this->view('admin/users/form', [
            'title' => $title,
            'user' => $this->user(),
            'adminUser' => $adminUser,
            'roles' => $this->roles->all(),
            'selectedRoles' => $selectedRoles,
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Users', 'url' => '/admin/users'], ['label' => $title]],
        ]);
    }
}
