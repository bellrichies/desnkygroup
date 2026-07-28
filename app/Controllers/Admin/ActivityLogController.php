<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Services\ActivityLogService;
use App\Support\DatabaseFactory;

/**
 * Activity audit log viewer.
 */
class ActivityLogController extends BaseController
{
    private ActivityLogService $activity;

    public function __construct()
    {
        $this->activity = new ActivityLogService(
            new ActivityLogRepository(DatabaseFactory::make())
        );
    }

    public function index(): string
    {
        $all = $this->activity->filter($_GET);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = count($all);
        return $this->view('admin/activity-logs/index', [
            'title' => 'Activity Logs',
            'user' => $this->user(),
            'activities' => array_slice($all, ($page - 1) * 10, 10),
            'pagination' => ['page' => $page, 'total_pages' => max(1, (int) ceil($total / 10)), 'total' => $total],
            'filters' => $_GET,
            'breadcrumbs' => [['label' => 'Activity Logs']],
        ]);
    }
}
