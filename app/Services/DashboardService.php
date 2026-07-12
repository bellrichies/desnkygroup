<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

/**
 * Prepares admin dashboard KPIs and recent activity sections.
 */
class DashboardService extends BaseService
{
    public function __construct(
        private DashboardRepository $dashboardRepository,
        private ActivityLogService $activityLogService
    ) {
    }

    /**
     * Get all dashboard KPI values.
     *
     * @return array<string, mixed>
     */
    public function getKpis(): array
    {
        return [
            'published_pages' => $this->safeCount('pages', 'is_published = 1 AND deleted_at IS NULL'),
            'active_hero_slides' => $this->safeCount('homepage_hero_sliders', 'is_active = 1'),
            'products' => $this->safeCount('products', 'is_active = 1 AND deleted_at IS NULL'),
            'pending_orders' => $this->safeCount('orders', 'order_status = ?', ['pending']),
            'completed_orders_total' => $this->safeSum('orders', 'total', 'order_status IN (?, ?)', ['delivered', 'completed']),
            'unread_inquiries' => $this->safeCount('contacts', 'status = ?', ['new']),
            'newsletter_subscribers' => $this->safeCount('newsletter_subscribers', 'status = ?', ['subscribed']),
            'low_stock_products' => $this->safeCount(
                'products',
                'quantity_in_stock <= reorder_level AND is_active = 1 AND deleted_at IS NULL'
            ),
            'admin_users' => $this->safeCount('admin_users', 'is_active = 1 AND deleted_at IS NULL'),
        ];
    }

    /**
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function getRecentInquiries(int $limit = 5): array
    {
        try {
            return $this->dashboardRepository->recentInquiries($limit);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function getRecentOrders(int $limit = 5): array
    {
        try {
            return $this->dashboardRepository->recentOrders($limit);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function getRecentActivities(int $limit = 10): array
    {
        return $this->activityLogService->recent($limit);
    }

    private function safeCount(string $table, string $where, array $params = []): int
    {
        try {
            return $this->dashboardRepository->count($table, $where, $params);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function safeSum(string $table, string $column, string $where, array $params = []): float
    {
        try {
            return $this->dashboardRepository->sum($table, $column, $where, $params);
        } catch (\Throwable) {
            return 0.0;
        }
    }
}
