<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * DashboardController - Handles admin dashboard
 */
class DashboardController extends BaseController
{
    /**
     * Display admin dashboard
     *
     * @return string
     */
    public function index(): string
    {
        // Get authenticated user
        $user = $this->user();

        return $this->view('admin/dashboard/index', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'stats' => [
                'pages' => 0,
                'products' => 0,
                'orders' => 0,
                'contacts' => 0,
            ],
        ]);
    }

    /**
     * Phase 1 placeholder for future admin modules.
     *
     * @return string
     */
    public function notImplemented(): string
    {
        http_response_code(501);
        return $this->view('admin/dashboard/index', [
            'title' => 'Coming Soon',
            'user' => $this->user(),
            'stats' => [
                'pages' => 0,
                'products' => 0,
                'orders' => 0,
                'contacts' => 0,
            ],
            'notice' => 'This admin module is planned for a later phase.',
        ]);
    }
}
