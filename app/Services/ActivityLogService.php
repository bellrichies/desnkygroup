<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;

/**
 * Records admin activity without exposing logging failures to users.
 */
class ActivityLogService extends BaseService
{
    public function __construct(private ActivityLogRepository $activityLogRepository)
    {
    }

    /**
     * Log an admin action.
     *
     * @param int|null $adminUserId Admin user ID.
     * @param string $action Action slug.
     * @param string $module Module slug.
     * @param string|null $description Description.
     * @return void
     */
    public function record(?int $adminUserId, string $action, string $module, ?string $description = null): void
    {
        try {
            $this->activityLogRepository->create($adminUserId, $action, $module, $description);
        } catch (\Throwable) {
            // Activity logging should not block the admin workflow.
        }
    }

    /**
     * Get recent activity rows.
     *
     * @param int $limit Number of rows.
     * @return array<int, array<string, mixed>>
     */
    public function recent(int $limit = 10): array
    {
        try {
            return $this->activityLogRepository->recent($limit);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function filter(array $filters = []): array
    {
        try {
            return $this->activityLogRepository->filter($filters);
        } catch (\Throwable) {
            return [];
        }
    }
}
