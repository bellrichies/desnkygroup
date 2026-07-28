<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use App\Services\ActivityLogService;
use App\Services\AdminUserService;

/**
 * AuthController - Handles authentication
 */
class AuthController extends BaseController
{
    public function __construct(
        private AdminUserService $adminUserService,
        private ActivityLogService $activityLogService
    ) {
    }

    /**
     * Display login form
     *
     * @return string
     */
    public function login(): string
    {
        // Redirect if already authenticated
        if ($this->isAuthenticated()) {
            $this->redirect('/admin/dashboard');
        }

        return $this->view('admin/auth/login', [
            'title' => 'Admin Login',
            'csrf_token' => $this->csrf(),
        ]);
    }

    /**
     * Handle login request
     *
     * @return string
     */
    public function authenticate(): string
    {
        // Validate CSRF token
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid security token. Please try again.',
            ], 419);
        }

        try {
            $data = $this->validate($_POST, [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
        } catch (ValidationException $exception) {
            return $this->json([
                'success' => false,
                'message' => 'Please enter a valid email address and password.',
                'errors' => $exception->getErrors(),
            ], 422);
        }

        $email = (string) $data['email'];
        if ($this->adminUserService->isLockedOut($email)) {
            $seconds = $this->adminUserService->lockoutSecondsRemaining($email);
            $this->activityLogService->record(null, 'failed_login_locked', 'auth', "Locked login attempt for {$email}");

            return $this->json([
                'success' => false,
                'message' => 'Too many failed attempts. Try again in ' . ceil($seconds / 60) . ' minutes.',
            ], 429);
        }

        $user = $this->adminUserService->authenticate($email, (string) $data['password']);

        if ($user !== null) {
            session_regenerate_id(true);
            $this->setUser($user->toSessionArray());
            $this->adminUserService->rememberCurrentSession($user->id);
            $this->activityLogService->record($user->id, 'login', 'auth', 'Admin login successful.');

            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => '/admin/dashboard',
            ], 200);
        }

        $this->activityLogService->record(null, 'failed_login', 'auth', "Failed login attempt for {$email}");

        return $this->json([
            'success' => false,
            'message' => 'Invalid email or password',
        ], 401);
    }

    /**
     * Handle logout
     *
     * @return void
     */
    public function logout(): void
    {
        $user = $this->user();
        $this->activityLogService->record(
            isset($user['id']) ? (int) $user['id'] : null,
            'logout',
            'auth',
            'Admin logout.'
        );
        $this->adminUserService->forgetCurrentSession();

        $this->clearUser();
        session_regenerate_id(true);
        $this->redirect('/admin/login');
    }
}
