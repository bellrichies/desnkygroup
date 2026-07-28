<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Repositories\AdminUserRepository;
use App\Repositories\RoleRepository;
use App\Security\Hasher;

/**
 * Business logic for admin authentication.
 */
class AdminUserService extends BaseService
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    public function __construct(
        private AdminUserRepository $adminUserRepository,
        private ?RoleRepository $roleRepository = null,
        private ?ActivityLogService $activityLogService = null
    ) {
    }

    /**
     * Authenticate an active admin user with email and password.
     *
     * @param string $email Email address.
     * @param string $password Plain password.
     * @return AdminUser|null
     */
    public function authenticate(string $email, string $password): ?AdminUser
    {
        $email = strtolower(trim($email));

        if ($this->isLockedOut($email)) {
            return null;
        }

        $record = $this->adminUserRepository->findActiveByEmail($email);
        if ($record === null) {
            $this->recordFailedAttempt($email);
            return null;
        }

        $user = new AdminUser($record);
        if (!$user->checkPassword($password)) {
            $this->recordFailedAttempt($email);
            return null;
        }

        $this->clearFailedAttempts($email);
        $this->adminUserRepository->updateLastLogin($user->id);
        $user->updateLastLogin();

        return $user;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(?string $search = null): array
    {
        return $this->adminUserRepository->all($search);
    }

    public function find(int $id): ?array
    {
        return $this->adminUserRepository->find($id);
    }

    /**
     * @return array<int, int>
     */
    public function roleIds(int $adminUserId): array
    {
        return $this->adminUserRepository->roleIds($adminUserId);
    }

    /**
     * @return array{user_id: int, temporary_password: string}
     */
    public function create(array $data, int $actorId): array
    {
        $payload = $this->prepare($data);
        $temporaryPassword = $data['password'] !== '' ? (string) $data['password'] : $this->temporaryPassword();
        $payload['password_hash'] = (new Hasher())->make($temporaryPassword);
        $payload['force_password_reset'] = true;

        $userId = $this->adminUserRepository->create($payload);
        $this->adminUserRepository->syncRoles($userId, $this->roleIdsFromPayload($data));
        $this->log($actorId, 'created', 'admins', 'Created admin user ' . $payload['email']);

        return ['user_id' => $userId, 'temporary_password' => $temporaryPassword];
    }

    public function update(int $id, array $data, int $actorId): bool
    {
        if ($id === $actorId && empty($data['is_active'])) {
            throw new \RuntimeException('You cannot suspend your own account.');
        }

        if ($this->wouldRemoveLastSuperAdmin($id, $data)) {
            throw new \RuntimeException('At least one active Super Admin must remain.');
        }

        $payload = $this->prepare($data);
        $this->adminUserRepository->update($id, $payload);
        $this->adminUserRepository->syncRoles($id, $this->roleIdsFromPayload($data));
        $this->log($actorId, 'updated', 'admins', 'Updated admin user ' . $payload['email']);

        return true;
    }

    public function delete(int $id, int $actorId): bool
    {
        if ($id === $actorId) {
            throw new \RuntimeException('You cannot delete your own account.');
        }

        if ($this->adminUserRepository->countSuperAdmins($id) < 1 && $this->isSuperAdmin($id)) {
            throw new \RuntimeException('The last Super Admin cannot be deleted.');
        }

        $this->adminUserRepository->softDelete($id);
        $this->log($actorId, 'deleted', 'admins', 'Deleted admin user #' . $id);

        return true;
    }

    public function resetPassword(int $id, int $actorId): string
    {
        $password = $this->temporaryPassword();
        $this->adminUserRepository->setPassword($id, (new Hasher())->make($password), true);
        $this->log($actorId, 'password_reset', 'admins', 'Reset password for admin user #' . $id);

        return $password;
    }

    public function forgetCurrentSession(): void
    {
        $this->adminUserRepository->removeSession(session_id());
    }

    public function rememberCurrentSession(int $adminUserId): void
    {
        $this->adminUserRepository->rememberSession($adminUserId, session_id());
    }

    /**
     * Determine if an email/IP pair is temporarily locked.
     *
     * @param string $email Email address.
     * @return bool
     */
    public function isLockedOut(string $email): bool
    {
        $key = $this->attemptKey($email);
        $attempt = $_SESSION['admin_login_attempts'][$key] ?? null;

        if (!is_array($attempt)) {
            return false;
        }

        if ((int) ($attempt['count'] ?? 0) < self::MAX_ATTEMPTS) {
            return false;
        }

        $lastAttempt = (int) ($attempt['last_attempt'] ?? 0);

        return (time() - $lastAttempt) < self::LOCKOUT_SECONDS;
    }

    /**
     * Seconds remaining in lockout window.
     *
     * @param string $email Email address.
     * @return int
     */
    public function lockoutSecondsRemaining(string $email): int
    {
        $key = $this->attemptKey($email);
        $attempt = $_SESSION['admin_login_attempts'][$key] ?? [];
        $lastAttempt = (int) ($attempt['last_attempt'] ?? 0);
        $remaining = self::LOCKOUT_SECONDS - (time() - $lastAttempt);

        return max(0, $remaining);
    }

    private function recordFailedAttempt(string $email): void
    {
        $key = $this->attemptKey($email);
        $attempt = $_SESSION['admin_login_attempts'][$key] ?? ['count' => 0, 'last_attempt' => 0];

        $_SESSION['admin_login_attempts'][$key] = [
            'count' => ((int) $attempt['count']) + 1,
            'last_attempt' => time(),
        ];
    }

    private function clearFailedAttempts(string $email): void
    {
        unset($_SESSION['admin_login_attempts'][$this->attemptKey($email)]);
    }

    private function attemptKey(string $email): string
    {
        return sha1(strtolower(trim($email)) . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        $fullName = trim((string) ($data['full_name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));

        if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('A valid name and email address are required.');
        }

        return [
            'full_name' => $fullName,
            'email' => $email,
            'role' => (string) ($data['legacy_role'] ?? 'editor'),
            'is_active' => isset($data['is_active']),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function roleIdsFromPayload(array $data): array
    {
        return array_map('intval', (array) ($data['role_ids'] ?? []));
    }

    private function wouldRemoveLastSuperAdmin(int $id, array $data): bool
    {
        if (!$this->isSuperAdmin($id)) {
            return false;
        }

        $newRoleIds = $this->roleIdsFromPayload($data);
        $roleRepository = $this->roleRepository;
        $hasSuperAdminRole = false;

        foreach ($newRoleIds as $roleId) {
            $role = $roleRepository?->find($roleId);
            if (($role['slug'] ?? null) === 'super-admin') {
                $hasSuperAdminRole = true;
                break;
            }
        }

        if ($hasSuperAdminRole && isset($data['is_active'])) {
            return false;
        }

        return $this->adminUserRepository->countSuperAdmins($id) < 1;
    }

    private function isSuperAdmin(int $id): bool
    {
        return in_array('super-admin', $this->adminUserRepository->roleSlugs($id), true);
    }

    private function temporaryPassword(): string
    {
        return 'DGR-' . bin2hex(random_bytes(4)) . '-' . random_int(100, 999);
    }

    private function log(int $actorId, string $action, string $module, string $description): void
    {
        $this->activityLogService?->record($actorId, $action, $module, $description);
    }
}
