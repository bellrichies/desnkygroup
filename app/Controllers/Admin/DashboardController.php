<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\DashboardService;

/**
 * DashboardController - Handles admin dashboard
 */
class DashboardController extends BaseController
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    /**
     * Display admin dashboard
     *
     * @return string
     */
    public function index(): string
    {
        $user = $this->user();

        return $this->view('admin/dashboard/index', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ],
            'kpis' => $this->dashboardService->getKpis(),
            'recentInquiries' => $this->dashboardService->getRecentInquiries(),
            'recentOrders' => $this->dashboardService->getRecentOrders(),
            'recentActivities' => $this->dashboardService->getRecentActivities(),
            'lastUpdated' => date('M j, Y g:i A'),
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
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['label' => 'Coming Soon', 'url' => null],
            ],
            'kpis' => $this->dashboardService->getKpis(),
            'recentInquiries' => $this->dashboardService->getRecentInquiries(),
            'recentOrders' => $this->dashboardService->getRecentOrders(),
            'recentActivities' => $this->dashboardService->getRecentActivities(),
            'lastUpdated' => date('M j, Y g:i A'),
            'notice' => 'This admin module is planned for a later phase.',
        ]);
    }
}
