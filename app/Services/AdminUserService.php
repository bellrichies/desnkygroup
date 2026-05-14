<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Repositories\AdminUserRepository;

/**
 * Business logic for admin authentication.
 */
class AdminUserService extends BaseService
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    public function __construct(private AdminUserRepository $adminUserRepository)
    {
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
}
