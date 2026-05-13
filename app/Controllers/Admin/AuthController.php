<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * AuthController - Handles authentication
 */
class AuthController extends BaseController
{
    /**
     * Display login form
     *
     * @return string
     */
    public function loginForm(): string
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
    public function login(): string
    {
        // Validate CSRF token
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid security token. Please try again.',
            ], 419);
        }

        // Validate input
        $data = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // In Phase 2+, would check against database
        // For now, use demo credentials

        if ($data['email'] === 'admin@example.com' && $data['password'] === 'password') {
            // Set session user
            $this->setUser([
                'id' => 1,
                'email' => $data['email'],
                'full_name' => 'Admin User',
                'role' => 'super_admin',
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => '/admin/dashboard',
            ], 200);
        }

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
        $this->clearUser();
        $this->redirect('/admin/login');
    }
}
